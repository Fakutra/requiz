<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Answer;
use App\Models\Test;
use App\Models\TestResult;
use App\Models\TestSection;
use App\Models\TestSectionResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class QuizController extends Controller
{
    // GET /quiz/{slug}
    public function start(Request $request, $slug)
    {
        $test = Test::with(['position', 'sections.questionBundle.questions'])
            ->where('slug', $slug)->firstOrFail();

        $applicant = Applicant::where('user_id', Auth::id())
            ->where('position_id', $test->position_id)->first();

        if (!$applicant) abort(403, 'Anda tidak terdaftar pada posisi ini.');

        // HARD END
        if ($test->test_end && now()->greaterThanOrEqualTo($test->test_end)) {
            $this->finishWholeTestNow($test, $applicant);
            return redirect()->route('quiz.finish', ['slug' => $slug])
                ->with('status', 'Tes telah berakhir.');
        }

        // Apakah sudah pernah mulai?
        $existing = TestResult::with('sectionResults')
            ->where('applicant_id', $applicant->id)
            ->where('test_id', $test->id)
            ->first();

        $alreadyStarted = $existing
            ? (bool) $existing->started_at || $existing->sectionResults->whereNotNull('started_at')->count() > 0
            : false;

        // Gate mulai pertama kali hanya saat window (test_date..test_closed)
        if (!$alreadyStarted && $test->test_date && $test->test_closed) {
            if (! now()->between($test->test_date, $test->test_closed, true)) {
                abort(403, 'Tombol belum aktif atau sudah ditutup.');
            }
        }

        // Buat/ambil TestResult
        $testResult = TestResult::firstOrCreate(
            ['applicant_id' => $applicant->id, 'test_id' => $test->id],
            ['started_at' => now()]
        )->load('sectionResults');

        // Ambil section urut
        $sections = $test->sections()
            ->with(['questionBundle.questions'])
            ->when(Schema::hasColumn('test_sections', 'order'), fn($q) => $q->orderBy('order'))
            ->orderBy('id')
            ->get();

        if ($sections->isEmpty()) abort(404, 'Test belum memiliki section.');

        // Tutup section expired oleh durasi
        $this->autoCloseExpiredSections($sections, $testResult, $applicant->id);

        // Hard end lagi setelah auto-close
        if ($test->test_end && now()->greaterThanOrEqualTo($test->test_end)) {
            $this->finishWholeTestNow($test, $applicant);
            return redirect()->route('quiz.finish', ['slug' => $slug])
                ->with('status', 'Tes telah berakhir.');
        }

        $testResult->load('sectionResults');

        // Section aktif = first unfinished
        $firstUnfinished = $sections->first(function ($s) use ($testResult) {
            $sr = $testResult->sectionResults->firstWhere('test_section_id', $s->id);
            return !$sr || !$sr->finished_at;
        });
        if (!$firstUnfinished) {
            if (!$testResult->finished_at) {
                $testResult->finished_at = now();
                $testResult->score = TestSectionResult::where('test_result_id', $testResult->id)->sum('score');
                $testResult->save();
            }
            return redirect()->route('quiz.finish', ['slug' => $slug]);
        }

        // Simpan id aktif di session (UI saja)
        $sessionKey = "quiz.{$test->id}.current_section_id";
        $currentSectionId = (int) session($sessionKey, $firstUnfinished->id);
        $srCurrent = $testResult->sectionResults->firstWhere('test_section_id', $currentSectionId);
        if ($srCurrent && $srCurrent->finished_at) {
            $currentSectionId = $firstUnfinished->id;
            session([$sessionKey => $currentSectionId]);
        }
        $currentSection = $sections->firstWhere('id', $currentSectionId);

        // Row section result aktif
        $sectionResult = TestSectionResult::firstOrCreate(
            ['test_result_id' => $testResult->id, 'test_section_id' => $currentSection->id]
        );
        if (is_null($sectionResult->started_at)) {
            $sectionResult->started_at = now();
            $sectionResult->save();
        }

        // ==== Persist shuffle (soal & opsi) ====
        $shuffle = $sectionResult->shuffle_state ?? [];
        // Urut soal
        $questions = optional($currentSection->questionBundle)->questions ?? collect();
        if (($currentSection->shuffle_questions ?? false) && empty($shuffle['question_order'])) {
            $shuffle['question_order'] = $questions->pluck('id')->shuffle()->values()->all();
        } elseif (empty($shuffle['question_order'])) {
            $shuffle['question_order'] = $questions->pluck('id')->values()->all();
        }
        // Peta opsi per soal (display A..E -> original A..E)
        if ($currentSection->shuffle_options ?? false) {
            $optionMaps = $shuffle['option_maps'] ?? [];
            foreach ($questions as $q) {
                if (!isset($optionMaps[$q->id])) {
                    $letters = ['A','B','C','D','E'];
                    // hanya huruf yang ada teksnya
                    $present = array_values(array_filter($letters, fn($L) => !empty($q->{'option_'.strtolower($L)})));
                    $shuffled = $present;
                    shuffle($shuffled);
                    // tampil A..E berisi huruf asli sesuai hasil shuffle
                    $map = [];
                    foreach (['A','B','C','D','E'] as $i => $slot) {
                        $map[$slot] = $shuffled[$i] ?? null; // bisa null kalau opsi tidak sampai E
                    }
                    $optionMaps[$q->id] = $map; // contoh: ["A"=>"C","B"=>"A","C"=>"E","D"=>"B","E"=>"D"]
                }
            }
            $shuffle['option_maps'] = $optionMaps;
        } else {
            // tidak shuffle → identitas
            $optionMaps = $shuffle['option_maps'] ?? [];
            foreach ($questions as $q) {
                if (!isset($optionMaps[$q->id])) {
                    $map = [];
                    foreach (['A','B','C','D','E'] as $L) {
                        $map[$L] = !empty($q->{'option_'.strtolower($L)}) ? $L : null;
                    }
                    $optionMaps[$q->id] = $map;
                }
            }
            $shuffle['option_maps'] = $optionMaps;
        }

        // simpan kalau ada yang baru
        if ($sectionResult->shuffle_state !== $shuffle) {
            $sectionResult->shuffle_state = $shuffle;
            $sectionResult->save();
        }

        // Terapkan urutan soal sesuai shuffle persisted
        $orderIndex = array_flip($shuffle['question_order']);
        $questions = $questions->sortBy(fn($q) => $orderIndex[$q->id] ?? PHP_INT_MAX)->values();

        // Deadline = min( started_at + durasi, test_end )
        $duration        = (int) $currentSection->duration_minutes;
        $sectionDeadline = $sectionResult->started_at->copy()->addMinutes($duration);
        $deadlineCarbon  = $test->test_end ? $sectionDeadline->min($test->test_end) : $sectionDeadline;
        $deadline = $deadlineCarbon->toIso8601String();

        // Jawaban tersimpan (huruf **asli**)
        $savedRaw = Answer::where('test_section_result_id', $sectionResult->id)
            ->get()->mapWithKeys(fn ($a) => [$a->question_id => (string) $a->answer]);

        // Siapkan data untuk view: opsi ditata A..E tapi teks diambil dari huruf asli via map
        $prepared = $questions->map(function ($q) use ($shuffle, $savedRaw) {
            $map = $shuffle['option_maps'][$q->id] ?? [];
            $displayOptions = [];
            foreach (['A','B','C','D','E'] as $L) {
                $orig = $map[$L] ?? null;
                if ($orig) {
                    $key = 'option_'.strtolower($orig);
                    $txt = $q->$key;
                    if (!empty($txt)) $displayOptions[$L] = $txt;
                }
            }

            // tentukan checked untuk tipe objektif (konversi dari huruf asli -> huruf tampil)
            $checked = [];
            $saved = $savedRaw[$q->id] ?? null;
            if (in_array($q->type, ['PG','Poin'])) {
                if ($saved) {
                    foreach (['A','B','C','D','E'] as $L) {
                        if (($map[$L] ?? null) === $saved) { $checked[] = $L; break; }
                    }
                }
            } elseif ($q->type === 'Multiple') {
                $origin = collect(explode(',', (string)$saved))->map(fn($x)=>strtoupper(trim($x)))->filter()->all();
                foreach (['A','B','C','D','E'] as $L) {
                    if (in_array($map[$L] ?? '', $origin, true)) $checked[] = $L;
                }
            } elseif ($q->type === 'Essay') {
                $checked = $saved; // raw text
            }

            return [
                'id'          => $q->id,
                'type'        => $q->type,
                'question'    => $q->question,
                'image_path'  => $q->image_path,
                'options'     => $displayOptions,      // A..E => teks
                'option_map'  => $map,                 // A..E (display) -> huruf asli
                'checked'     => $checked,             // array huruf display / string essay
            ];
        });

        return response()->view('quiz', [
            'test'           => $test,
            'sections'       => $sections,
            'currentSection' => $currentSection,
            'questions'      => $prepared,
            'testResult'     => $testResult,
            'sectionResult'  => $sectionResult,
            'deadline'       => $deadline,
        ])->header('Cache-Control','no-store, no-cache, must-revalidate, max-age=0')
          ->header('Pragma','no-cache');
    }

    // POST /quiz/{slug}
    public function submitSection(Request $request, $slug)
    {
        $request->validate([
            'test_id'    => 'required|integer',
            'section_id' => 'required|integer|exists:test_sections,id',
            'answers'    => 'array',
        ]);

        $test = Test::with(['sections'])->where('slug', $slug)->firstOrFail();
        $applicant = Applicant::where('user_id', Auth::id())
            ->where('position_id', $test->position_id)->firstOrFail();
        $testResult = TestResult::where('applicant_id', $applicant->id)
            ->where('test_id', $test->id)->firstOrFail()->load('sectionResults');

        // HARD END
        if ($test->test_end && now()->greaterThanOrEqualTo($test->test_end)) {
            $this->finishWholeTestNow($test, $applicant);
            return redirect()->route('quiz.finish', ['slug' => $slug])
                ->with('status', 'Tes telah berakhir.');
        }

        $section = TestSection::with(['questionBundle.questions'])
            ->where('id', $request->integer('section_id'))
            ->where('test_id', $test->id)->firstOrFail();

        $sectionResult = TestSectionResult::firstOrCreate(
            ['test_result_id' => $testResult->id, 'test_section_id' => $section->id]
        );
        if (is_null($sectionResult->started_at)) {
            $sectionResult->started_at = now();
            $sectionResult->save();
        }

        // Validasi urutan: first unfinished
        $ordered = $test->sections()
            ->when(Schema::hasColumn('test_sections', 'order'), fn($q)=>$q->orderBy('order'))
            ->orderBy('id')->get();

        $firstUnfinished = $ordered->first(function ($s) use ($testResult) {
            $sr = $testResult->sectionResults->firstWhere('test_section_id', $s->id);
            return !$sr || !$sr->finished_at;
        });
        if ($firstUnfinished && $section->id !== $firstUnfinished->id) {
            abort(403, 'Urutan section tidak valid.');
        }
        session(["quiz.{$test->id}.current_section_id" => $section->id]);

        // Deadline server (min durasi section, hard end)
        $duration = (int) $section->duration_minutes;
        $deadline = $sectionResult->started_at->copy()->addMinutes($duration);
        if ($test->test_end) $deadline = $deadline->min($test->test_end);
        $isTimeUp = now()->greaterThanOrEqualTo($deadline);

        // ==== KONVERSI huruf display -> huruf asli berdasar shuffle_state ====
        $inputAnswers = $this->unshuffleIncomingAnswers(
            $request->input('answers', []),
            $sectionResult->shuffle_state['option_maps'] ?? []
        );

        // Simpan jawaban
        $this->saveAnswers($inputAnswers, $section, $testResult, $sectionResult, $applicant->id);
        $this->ensureAllAnswersExistAndScore($section, $applicant->id, $testResult, $sectionResult);

        // Tutup section
        if (!$sectionResult->finished_at) {
            $sectionResult->finished_at = now();
            $sectionResult->score = Answer::where('test_section_result_id', $sectionResult->id)->sum('score');
            $sectionResult->save();
        }

        // Next section
        $ids = $ordered->pluck('id')->values();
        $currentIndex = $ids->search($section->id);
        $nextId = ($currentIndex !== false && $currentIndex + 1 < $ids->count()) ? $ids[$currentIndex + 1] : null;

        if ($nextId) {
            session(["quiz.{$test->id}.current_section_id" => $nextId]);
            $signed = URL::signedRoute('quiz.start', ['slug' => $slug]);
            return redirect()->to($signed)
                ->with('status', $isTimeUp
                    ? 'Waktu habis. Beralih ke section berikutnya.'
                    : 'Section tersimpan. Lanjut ke section berikutnya.');
        }

        // Selesai semua section
        if (!$testResult->finished_at) {
            $testResult->finished_at = now();
            $testResult->score = TestSectionResult::where('test_result_id', $testResult->id)->sum('score');
            $testResult->save();
        }
        session()->forget("quiz.{$test->id}.current_section_id");

        return redirect()->route('quiz.finish', ['slug' => $slug]);
    }

    // POST /quiz/{slug}/autosave
    public function autosave(Request $request, $slug)
    {
        $request->validate([
            'section_id' => 'required|integer|exists:test_sections,id',
            'answers'    => 'required|array',
        ]);

        $test = Test::with('sections')->where('slug', $slug)->firstOrFail();

        if ($test->test_end && now()->greaterThanOrEqualTo($test->test_end)) {
            return response()->json(['ok' => false, 'message' => 'Tes telah berakhir'], 409);
        }

        $applicant = Applicant::where('user_id', Auth::id())
            ->where('position_id', $test->position_id)->firstOrFail();
        $testResult = TestResult::where('applicant_id', $applicant->id)
            ->where('test_id', $test->id)->firstOrFail()->load('sectionResults');

        $section = TestSection::with('questionBundle.questions')
            ->where('id', $request->integer('section_id'))
            ->where('test_id', $test->id)->firstOrFail();

        $sectionResult = TestSectionResult::firstOrCreate(
            ['test_result_id' => $testResult->id, 'test_section_id' => $section->id]
        );
        if (is_null($sectionResult->started_at)) {
            $sectionResult->started_at = now();
            $sectionResult->save();
        }

        // hanya boleh di section aktif
        $ordered = $test->sections()
            ->when(Schema::hasColumn('test_sections', 'order'), fn($q)=>$q->orderBy('order'))
            ->orderBy('id')->get();

        $firstUnfinished = $ordered->first(function ($s) use ($testResult) {
            $sr = $testResult->sectionResults->firstWhere('test_section_id', $s->id);
            return !$sr || !$sr->finished_at;
        });
        if ($firstUnfinished && $section->id !== $firstUnfinished->id) {
            return response()->json(['ok' => false, 'message' => 'Section bukan yang aktif'], 409);
        }

        // Konversi display -> asli
        $inputAnswers = $this->unshuffleIncomingAnswers(
            $request->input('answers', []),
            $sectionResult->shuffle_state['option_maps'] ?? []
        );

        $this->saveAnswers($inputAnswers, $section, $testResult, $sectionResult, $applicant->id, true);

        return response()->json(['ok' => true, 'saved_at' => now()->toDateTimeString()]);
    }

    public function finish(Request $request, $slug)
    {
        $test = Test::where('slug', $slug)->firstOrFail();
        $applicant = Applicant::where('user_id', Auth::id())
            ->where('position_id', $test->position_id)->firstOrFail();

        $result = TestResult::with(['sectionResults.testSection'])
            ->where('applicant_id', $applicant->id)
            ->where('test_id', $test->id)->firstOrFail();

        return view('quiz-finish', ['test' => $test, 'result' => $result]);
    }

    /** Utilities **/

    // Konversi huruf tampilan (A..E) -> huruf asli sesuai map
    private function unshuffleIncomingAnswers(array $inputAnswers, array $optionMaps): array
    {
        $out = [];
        foreach ($inputAnswers as $qid => $val) {
            $map = $optionMaps[$qid] ?? [];
            if (is_array($val)) {
                $letters = array_map(function ($d) use ($map) {
                    $d = strtoupper(trim((string)$d));
                    return $map[$d] ?? $d;
                }, $val);
                sort($letters);
                $out[$qid] = implode(',', $letters);
            } else {
                $d = strtoupper(trim((string)$val));
                $out[$qid] = $map[$d] ?? $d;
            }
        }
        return $out;
    }

    private function saveAnswers(array $inputAnswers, TestSection $section, TestResult $testResult, TestSectionResult $sectionResult, int $applicantId, bool $withScore = true): void
    {
        $questions = optional($section->questionBundle)->questions ?? collect();

        DB::transaction(function () use ($inputAnswers, $questions, $applicantId, $section, $testResult, $sectionResult, $withScore) {
            foreach ($questions as $q) {
                $qid = $q->id;
                if (!array_key_exists($qid, $inputAnswers)) continue;

                $normalized = strtoupper(trim((string) $inputAnswers[$qid]));

                $score = null;
                if ($withScore) {
                    switch ($q->type) {
                        case 'PG':
                            $score = (strtoupper((string)$q->answer) === $normalized) ? 1 : 0; break;
                        case 'Multiple':
                            $correct = collect(explode(',', (string)$q->answer))->map(fn($x)=>strtoupper(trim($x)))->filter();
                            $user    = collect(explode(',', $normalized))->map(fn($x)=>strtoupper(trim($x)))->filter();
                            $score = $user->count() && $user->diff($correct)->isEmpty() && $correct->diff($user)->isEmpty() ? 1 : 0; break;
                        case 'Poin':
                            $map = ['A'=>$q->point_a,'B'=>$q->point_b,'C'=>$q->point_c,'D'=>$q->point_d,'E'=>$q->point_e];
                            $score = $map[$normalized] ?? 0; break;
                        case 'Essay':
                            $score = null; break;
                    }
                }

                Answer::updateOrCreate(
                    [
                        'applicant_id'           => $applicantId,
                        'question_id'            => $qid,
                        'test_section_id'        => $section->id,
                        'test_result_id'         => $testResult->id,
                        'test_section_result_id' => $sectionResult->id,
                    ],
                    ['answer' => $normalized, 'score' => $score]
                );
            }
        });
    }

    private function ensureAllAnswersExistAndScore(TestSection $section, int $applicantId, TestResult $testResult, TestSectionResult $sectionResult): void
    {
        $questions = optional($section->questionBundle)->questions ?? collect();

        $existingQids = Answer::where('test_section_result_id', $sectionResult->id)
            ->pluck('question_id')->all();

        foreach ($questions as $q) {
            if (!in_array($q->id, $existingQids, true)) {
                $blankScore = match ($q->type) {
                    'PG','Multiple','Poin' => 0,
                    default => null,
                };

                Answer::create([
                    'applicant_id'           => $applicantId,
                    'question_id'            => $q->id,
                    'test_section_id'        => $section->id,
                    'test_result_id'         => $testResult->id,
                    'test_section_result_id' => $sectionResult->id,
                    'answer'                 => '',
                    'score'                  => $blankScore,
                ]);
            }
        }
    }

    private function autoCloseExpiredSections($sections, TestResult $testResult, int $applicantId): void
    {
        foreach ($sections as $section) {
            $sr = $testResult->sectionResults->firstWhere('test_section_id', $section->id);
            if (!$sr) continue;
            if ($sr->finished_at) continue;
            if (is_null($sr->started_at)) continue;

            $deadline = $sr->started_at->copy()->addMinutes((int)$section->duration_minutes);
            if (Carbon::now()->lessThan($deadline)) break;

            DB::transaction(function () use ($section, $testResult, $sr, $applicantId) {
                $this->ensureAllAnswersExistAndScore($section, $applicantId, $testResult, $sr);
                if (!$sr->finished_at) {
                    $sr->finished_at = now();
                    $sr->score = Answer::where('test_section_result_id', $sr->id)->sum('score');
                    $sr->save();
                }
            });
        }
    }

    private function finishWholeTestNow(Test $test, Applicant $applicant): void
    {
        $testResult = TestResult::with(['sectionResults'])
            ->where('applicant_id', $applicant->id)
            ->where('test_id', $test->id)
            ->first();

        if (!$testResult) return;

        $sections = $test->sections()
            ->when(Schema::hasColumn('test_sections', 'order'), fn($q)=>$q->orderBy('order'))
            ->orderBy('id')->get();

        foreach ($sections as $section) {
            $sr = $testResult->sectionResults->firstWhere('test_section_id', $section->id);
            if (!$sr || $sr->finished_at) continue;

            DB::transaction(function () use ($section, $applicant, $testResult, $sr) {
                $this->ensureAllAnswersExistAndScore($section, $applicant->id, $testResult, $sr);
                $sr->finished_at = now();
                $sr->score = Answer::where('test_section_result_id', $sr->id)->sum('score');
                $sr->save();
            });
        }

        if (!$testResult->finished_at) {
            $testResult->finished_at = now();
            $testResult->score = TestSectionResult::where('test_result_id', $testResult->id)->sum('score');
            $testResult->save();
        }
    }
}

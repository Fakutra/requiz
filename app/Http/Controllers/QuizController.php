<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Answer;
use App\Models\Position;
use App\Models\Question;
use App\Models\Test;
use App\Models\TestResult;
use App\Models\TestSection;
use App\Models\TestSectionResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class QuizController extends Controller
{
    // GET /quiz/{slug}
    public function start(Request $request, $slug)
    {
        // Ambil test berdasarkan slug
        $test = Test::with(['position', 'sections.questionBundle.questions'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Pastikan user adalah applicant pada posisi test ini
        $applicant = Applicant::where('user_id', Auth::id())
            ->where('position_id', $test->position_id)
            ->first();

        if (!$applicant) {
            abort(403, 'Anda tidak terdaftar pada posisi ini.');
        }

        // Ambil/buat TestResult
        $testResult = TestResult::firstOrCreate(
            ['applicant_id' => $applicant->id, 'test_id' => $test->id],
            ['started_at' => now()]
        );

        // Urutkan sections (pakai kolom 'order' jika ada)
        $sections = $test->sections()
            ->with(['questionBundle.questions'])
            ->when(Schema::hasColumn('test_sections', 'order'), fn ($q) => $q->orderBy('order'))
            ->orderBy('id')
            ->get();

        if ($sections->isEmpty()) {
            abort(404, 'Test belum memiliki section.');
        }

        // Tentukan section aktif dari session (default: pertama)
        $sessionKey = "quiz.{$test->id}.current_section_id";
        $currentSectionId = session($sessionKey, $sections->first()->id);

        if (!$sections->pluck('id')->contains($currentSectionId)) {
            $currentSectionId = $sections->first()->id;
            session([$sessionKey => $currentSectionId]);
        }

        $currentSection = $sections->firstWhere('id', $currentSectionId);

        // Ambil/buat TestSectionResult untuk section aktif
        $sectionResult = TestSectionResult::firstOrCreate(
            ['test_result_id' => $testResult->id, 'test_section_id' => $currentSection->id]
        );

        // Siapkan deadline timer per section
        $deadlineKey = "quiz.section.{$currentSection->id}.deadline";
        $duration = (int) $currentSection->duration_minutes;
        $deadline = session($deadlineKey);

        if (!$deadline) {
            $deadline = Carbon::now()->addMinutes($duration)->toIso8601String();
            session([$deadlineKey => $deadline]);
        }

        // Ambil pertanyaan (safe)
        $questions = optional($currentSection->questionBundle)->questions ?? collect();

        // Shuffle jika diaktifkan
        if ($currentSection->shuffle_questions) {
            $questions = $questions->shuffle();
        }

        return view('quiz', [
            'test'           => $test,
            'sections'       => $sections,
            'currentSection' => $currentSection,
            'questions'      => $questions,
            'testResult'     => $testResult,
            'sectionResult'  => $sectionResult,
            'deadline'       => $deadline,
        ]);
    }

    // POST /quiz/{slug}
    public function submitSection(Request $request, $slug)
    {
        $request->validate([
            'test_id'    => 'required|integer',
            'section_id' => 'required|integer|exists:test_sections,id',
            'answers'    => 'array', // answers[question_id] => value atau array
        ]);

        $test = Test::with(['sections'])->where('slug', $slug)->firstOrFail();

        // Applicant milik user pada posisi test
        $applicant = Applicant::where('user_id', Auth::id())
            ->where('position_id', $test->position_id)
            ->firstOrFail();

        $testResult = TestResult::where('applicant_id', $applicant->id)
            ->where('test_id', $test->id)
            ->firstOrFail();

        $section = TestSection::with(['questionBundle.questions'])
            ->where('id', $request->integer('section_id'))
            ->where('test_id', $test->id)
            ->firstOrFail();

        $sectionResult = TestSectionResult::firstOrCreate(
            ['test_result_id' => $testResult->id, 'test_section_id' => $section->id]
        );

        // Cek timer
        $deadlineKey = "quiz.section.{$section->id}.deadline";
        $deadline = session($deadlineKey) ? Carbon::parse(session($deadlineKey)) : Carbon::now();
        $now = Carbon::now();
        $isTimeUp = $now->greaterThan($deadline);

        // Simpan jawaban
        $inputAnswers = $request->input('answers', []); // [question_id => 'A' | ['A','B'] | 'essay']
        $questions = optional($section->questionBundle)->questions ?? collect();

        DB::transaction(function () use ($inputAnswers, $questions, $applicant, $section, $testResult, $sectionResult) {
            foreach ($questions as $q) {
                $qid = $q->id;
                if (!array_key_exists($qid, $inputAnswers)) {
                    continue; // tidak dijawab
                }

                $userAns = $inputAnswers[$qid];

                // Normalisasi: Multiple -> "A,B"; PG/Poin -> "A"; Essay -> teks
                if (is_array($userAns)) {
                    $tmp = array_map(fn ($x) => strtoupper(trim((string) $x)), $userAns);
                    sort($tmp);
                    $normalized = implode(',', $tmp);
                } else {
                    $normalized = strtoupper(trim((string) $userAns));
                }

                // Hitung skor dasar
                $score = null;
                switch ($q->type) {
                    case 'PG':
                        $score = (strtoupper((string) $q->answer) === $normalized) ? 1 : 0;
                        break;

                    case 'Multiple':
                        $correct = collect(explode(',', (string) $q->answer))
                            ->map(fn ($x) => strtoupper(trim($x)))
                            ->filter();
                        $user = collect(explode(',', $normalized))
                            ->map(fn ($x) => strtoupper(trim($x)))
                            ->filter();
                        $score = $user->count() &&
                                 $user->diff($correct)->isEmpty() &&
                                 $correct->diff($user)->isEmpty() ? 1 : 0;
                        break;

                    case 'Poin':
                        $map = [
                            'A' => $q->point_a,
                            'B' => $q->point_b,
                            'C' => $q->point_c,
                            'D' => $q->point_d,
                            'E' => $q->point_e,
                        ];
                        $score = $map[$normalized] ?? 0;
                        break;

                    case 'Essay':
                        $score = null; // dinilai manual
                        break;
                }

                Answer::updateOrCreate(
                    [
                        'applicant_id'           => $applicant->id,
                        'question_id'            => $qid,
                        'test_section_id'        => $section->id,          // PENTING: ikut kunci
                        'test_result_id'         => $testResult->id,
                        'test_section_result_id' => $sectionResult->id,
                    ],
                    [
                        'answer' => $normalized,
                        'score'  => $score,
                    ]
                );
            }

            // Tutup section result (sekali saja)
            if (!$sectionResult->finished_at) {
                $sectionResult->finished_at = now();
                $sectionResult->score = Answer::where('test_section_result_id', $sectionResult->id)->sum('score');
                $sectionResult->save();
            }
        });

        // Tentukan section berikutnya
        $sections = $test->sections()
            ->when(Schema::hasColumn('test_sections', 'order'), fn ($q) => $q->orderBy('order'))
            ->orderBy('id')
            ->pluck('id')
            ->values();

        $currentIndex = $sections->search($section->id);
        $nextId = ($currentIndex !== false && $currentIndex + 1 < $sections->count())
            ? $sections[$currentIndex + 1]
            : null;

        // Hapus deadline section saat ini
        session()->forget($deadlineKey);

        if ($nextId) {
            // Set section berikutnya + deadline baru
            session(["quiz.{$test->id}.current_section_id" => $nextId]);

            $next = TestSection::findOrFail($nextId);
            session(["quiz.section.{$nextId}.deadline" => Carbon::now()->addMinutes((int) $next->duration_minutes)->toIso8601String()]);

            return redirect()->route('quiz.start', ['slug' => $slug])
                ->with('status', $isTimeUp ? 'Waktu habis. Beralih ke section berikutnya.' : 'Section tersimpan. Lanjut ke section berikutnya.');
        }

        // Semua section selesai → tutup testResult
        if (!$testResult->finished_at) {
            $testResult->finished_at = now();
            $testResult->score = TestSectionResult::where('test_result_id', $testResult->id)->sum('score');
            $testResult->save();
        }

        // Bersihkan session quiz untuk test ini
        session()->forget("quiz.{$test->id}.current_section_id");

        return redirect()->route('quiz.finish', ['slug' => $slug]);
    }

    // GET /quiz/{slug}/finish
    public function finish(Request $request, $slug)
    {
        $test = Test::where('slug', $slug)->firstOrFail();

        $applicant = Applicant::where('user_id', Auth::id())
            ->where('position_id', $test->position_id)
            ->firstOrFail();

        $result = TestResult::with(['sectionResults.testSection'])
            ->where('applicant_id', $applicant->id)
            ->where('test_id', $test->id)
            ->firstOrFail();

        return view('quiz-finish', [
            'test'   => $test,
            'result' => $result,
        ]);
    }
}

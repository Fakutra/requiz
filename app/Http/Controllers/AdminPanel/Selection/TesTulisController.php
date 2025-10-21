<?php

namespace App\Http\Controllers\AdminPanel\Selection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\Batch;
use App\Models\Position;
use App\Services\SelectionLogger;
use Illuminate\Support\Facades\Auth;
use App\Exports\TesTulisApplicantsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\SelectionNotifier;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\DB; // ⬅️ untuk query personality_rules

class TesTulisController extends Controller
{
    protected string $stage = 'Tes Tulis';

    /**
     * Halaman daftar peserta Tes Tulis
     */
    public function index(Request $request)
    {
        $batchId    = $request->query('batch');
        $positionId = $request->query('position');
        $search     = trim((string) $request->query('search'));
        $status     = $request->query('status');

        $batches   = Batch::orderBy('id')->get();
        $positions = $batchId ? Position::where('batch_id', $batchId)->get() : collect();

        $sort      = $request->query('sort', 'name');
        $direction = $request->query('direction', 'asc');

        $allowedSorts = ['name', 'total_nilai'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'name';
        }

        $q = Applicant::with([
            'position',
            'batch',
            'latestEmailLog',
            'latestTestResult.sectionResults.testSection',
            'latestTestResult.sectionResults.testSection.questionBundle.questions',
            'latestTestResult.sectionResults.answers.question',
        ])
        ->where('batch_id', $batchId)
        ->whereIn('status', [
            'Tes Tulis',
            'Technical Test',
            'Interview',
            'Offering',
            'Menerima Offering',
            'Tidak Lolos Tes Tulis',
            'Tidak Lolos Technical Test',
            'Tidak Lolos Interview',
            'Menolak Offering',
        ]);

        if ($positionId) $q->where('position_id', $positionId);
        if ($status) $q->where('status', $status);

        if ($search !== '') {
            $keyword = "%".mb_strtolower($search)."%";
            $q->where(function ($w) use ($keyword) {
                $w->whereRaw('LOWER(name) LIKE ?', [$keyword])
                  ->orWhereRaw('LOWER(email) LIKE ?', [$keyword])
                  ->orWhereRaw('LOWER(jurusan) LIKE ?', [$keyword]);
            });
        }

        // 🔹 Ambil semua data dengan relasi yang dibutuhkan
        $applicants = $q->get()->map(function ($a) {
            // Ambil setiap section 1–5 berdasarkan urutan testSection->order
            $sectionScores = [];
            for ($i = 1; $i <= 5; $i++) {
                $sectionScores[$i] = optional(
                    $a->latestTestResult?->sectionResults
                        ->first(fn($s) => $s->testSection && $s->testSection->order == $i)
                )->score ?? null;
            }

            // Simpan skor section & total ke properti virtual
            $a->section_1 = $sectionScores[1];
            $a->section_2 = $sectionScores[2];
            $a->section_3 = $sectionScores[3];
            $a->section_4 = $sectionScores[4];
            $a->section_5 = $sectionScores[5];
            $a->total_nilai = $a->latestTestResult?->score ?? null;

            return $a;
        });

        // 🔹 Sorting manual berdasarkan kolom
        if (in_array($sort, ['section_1','section_2','section_3','section_4','section_5','total_nilai'])) {
            $applicants = $applicants->sortBy($sort, SORT_REGULAR, $direction === 'desc');
        } else {
            $applicants = $applicants->sortBy($sort, SORT_NATURAL | SORT_FLAG_CASE, $direction === 'desc');
        }

        // 🔹 Pagination manual
        $page = request('page', 1);
        $perPage = 20;
        $applicants = new \Illuminate\Pagination\LengthAwarePaginator(
            $applicants->forPage($page, $perPage),
            $applicants->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.applicant.seleksi.tes-tulis.index', compact(
            'batches', 'positions', 'batchId', 'positionId', 'applicants'
        ));


        // ============================================================
        //  HITUNG FINAL TOTAL + MAX TOTAL PER APPLICANT
        // ============================================================
        foreach ($applicants as $a) {
            $testResult = $a->latestTestResult;
            if (!$testResult) {
                $a->final_total_score = null;
                $a->max_total_score   = null;
                continue;
            }

            $finalTotal  = 0;
            $maxTotal    = 0;

            foreach ($testResult->sectionResults as $sr) {
                $section = $sr->testSection;
                if (!$section) continue;

                $rawScore = (float) ($sr->score ?? 0);
                $questions = $section->questionBundle->questions ?? collect();

                $isPersonality = $questions->contains(fn($q) => $q->type === 'Poin');

                // ========================
                // HITUNG MAX SECTION (RAW)
                // ========================
                if ($isPersonality) {
                    $maxSection = $questions->count() * 5; // 5 per soal
                } else {
                    $pgCount     = $questions->where('type','PG')->count();
                    $multiCount  = $questions->where('type','Multiple')->count();
                    $essayCount  = $questions->where('type','Essay')->count();

                    $maxSection = ($pgCount * 1) + ($multiCount * 1) + ($essayCount * 3);
                }

                $maxTotal += $maxSection;

                // ========================
                // HITUNG FINAL (untuk total nilai)
                // ========================
                if ($isPersonality) {
                    $percent = $maxSection > 0 ? ($rawScore / $maxSection) * 100 : 0;

                    $rule = DB::table('personality_rules')
                        ->where('min_percentage', '<=', $percent)
                        ->where(function ($q) use ($percent) {
                            $q->where('max_percentage', '>=', $percent)
                              ->orWhereNull('max_percentage');
                        })
                        ->orderByDesc('min_percentage')
                        ->first();

                    $finalScore = $rule ? (int) $rule->score_value : 0;
                    $finalTotal += $finalScore;
                } else {
                    $finalTotal += $rawScore;
                }
            }

            $a->final_total_score = $finalTotal;
            $a->max_total_score   = $maxTotal;
        }

        return view('admin.applicant.seleksi.tes-tulis.index', compact(
            'batches', 'positions', 'batchId', 'positionId', 'applicants'
        ));
    }

    /**
     * Simpan nilai essay
     */
    public function scoreEssay(Request $request)
    {
        $data = $request->validate([
            'section_result_ids'   => 'required|array',
            'section_result_ids.*' => 'exists:test_section_results,id',
            'answer_scores'        => 'required|array',
        ]);

        foreach ($data['section_result_ids'] as $sectionResultId) {
            $sectionResult = \App\Models\TestSectionResult::findOrFail($sectionResultId);

            $totalScore = 0;
            $count = 0;

            foreach ($data['answer_scores'][$sectionResultId] ?? [] as $answerId => $score) {
                $answer = \App\Models\Answer::where('test_section_result_id', $sectionResult->id)->find($answerId);
                if ($answer) {
                    $answer->score = $score;
                    $answer->save();
                    $totalScore += (int) $score;
                    $count++;
                }
            }

            $sectionResult->score = $count > 0 ? $totalScore : null;
            $sectionResult->save();

            $testResult = $sectionResult->testResult;
            if ($testResult) {
                $total = $testResult->sectionResults()->whereNotNull('score')->sum('score');
                $testResult->score = $total > 0 ? $total : null;
                $testResult->save();
            }
        }

        // 🧩 catat aktivitas admin
        ActivityLogger::log(
            'update_score',
            'Tes Tulis',
            Auth::user()->name . ' memperbarui nilai essay peserta Tes Tulis'
        );

        return back()->with('success', 'Nilai essay berhasil disimpan.');
    }

    /**
     * Bulk update hasil Tes Tulis (lolos / gagal)
     */
    public function bulkMark(Request $r)
    {
        $data = $r->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:applicants,id',
            'bulk_action' => 'required|in:lolos,tidak_lolos',
        ]);

        foreach ($data['ids'] as $id) {
            $a = Applicant::find($id);

            SelectionLogger::write($a, $this->stage, $data['bulk_action'], Auth::id());

            $newStatus = $this->newStatus($data['bulk_action'], $a->status);
            $a->forceFill(['status' => $newStatus])->save();

            SelectionNotifier::notify($a, $this->stage, $data['bulk_action'], $newStatus);

            // 🧩 log aktivitas admin
            ActivityLogger::log(
                $data['bulk_action'],
                'Tes Tulis',
                Auth::user()->name . " menandai peserta {$a->name} sebagai '".strtoupper($data['bulk_action'])."'",
                "Applicant ID: {$a->id}"
            );
        }

        return back()->with('success', count($data['ids']).' peserta diperbarui.');
    }

    private function newStatus(string $result, string $current): string
    {
        return $result === 'lolos' ? 'Technical Test' : 'Tidak Lolos Tes Tulis';
    }

    /**
     * Export Excel peserta Tes Tulis
     */
    public function export(Request $r)
    {
        // ActivityLogger::log(
        //     'export',
        //     'Tes Tulis',
        //     Auth::user()->name.' mengekspor data peserta Tes Tulis'
        // );

        return Excel::download(
            new TesTulisApplicantsExport(
                $r->query('batch'),
                $r->query('position'),
                $r->query('search')
            ),
            'seleksi-tes-tulis.xlsx'
        );
    }
}

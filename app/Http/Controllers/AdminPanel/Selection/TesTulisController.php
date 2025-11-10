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
use Illuminate\Support\Facades\DB; // untuk personality_rules

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

        $allowedSorts = ['name', 'section_1', 'section_2', 'section_3', 'section_4', 'section_5', 'total_nilai'];
        if (!in_array($sort, $allowedSorts, true)) $sort = 'name';

        // Query peserta (tanpa relasi user/profile)
        $q = Applicant::with([
            'position',
            'batch',
            'latestEmailLog',
            'latestTestResult.test',
            'latestTestResult.sectionResults.testSection',
            'latestTestResult.sectionResults.testSection.questionBundle.questions',
            'latestTestResult.sectionResults.answers.question',
        ])
        ->where('batch_id', $batchId)
        ->whereIn('status', [
            'Tes Tulis','Technical Test','Interview','Offering','Menerima Offering',
            'Tidak Lolos Tes Tulis','Tidak Lolos Technical Test','Tidak Lolos Interview','Menolak Offering',
        ]);

        if ($positionId) $q->where('position_id', $positionId);
        if ($status)     $q->where('status', $status);

        // 🔎 Search ke kolom di applicants + relasi position
        if ($search !== '') {
            $keyword = '%'.mb_strtolower($search).'%';
            $q->where(function ($w) use ($keyword) {
                $w->whereRaw('LOWER(applicants.name) LIKE ?', [$keyword])
                  ->orWhereRaw('LOWER(applicants.email) LIKE ?', [$keyword])
                  ->orWhereRaw('LOWER(applicants.jurusan) LIKE ?', [$keyword])
                  ->orWhereHas('position', fn($p) =>
                      $p->whereRaw('LOWER(name) LIKE ?', [$keyword])
                  );
            });
        }

        // Ambil peserta
        $applicants = $q->get();

        // Max personality score KHUSUS batch ini
        $maxPersonalityFinal = (int) DB::table('personality_rules')
            ->where('batch_id', $batchId)
            ->max('score_value') ?: 0;

        // Hitung final_total_score & max_total_score (umur pakai accessor $a->age)
        foreach ($applicants as $a) {
            $testResult = $a->latestTestResult;
            if (!$testResult) {
                $a->final_total_score = null;
                $a->max_total_score   = null;
                continue;
            }

            $finalTotal = 0;
            $maxTotal   = 0;

            foreach ($testResult->sectionResults as $sr) {
                $section = $sr->testSection;
                if (!$section) continue;

                $rawScore  = (float) ($sr->score ?? 0);
                $questions = $section->questionBundle->questions ?? collect();
                $isPersonality = $questions->contains(fn($qq) => $qq->type === 'Poin');

                // MAX
                if ($isPersonality) {
                    $maxTotal += $maxPersonalityFinal;
                } else {
                    $pg    = $questions->where('type','PG')->count();
                    $multi = $questions->where('type','Multiple')->count();
                    $essay = $questions->where('type','Essay')->count();
                    $maxTotal += ($pg * 1) + ($multi * 1) + ($essay * 3);
                }

                // FINAL
                if ($isPersonality) {
                    $rawMaxSection = $questions->count() * 5;
                    $percent = $rawMaxSection > 0 ? ($rawScore / $rawMaxSection) * 100 : 0;

                    $rule = DB::table('personality_rules')
                        ->where('batch_id', $batchId)
                        ->where('min_percentage', '<=', $percent)
                        ->where(function ($qq) use ($percent) {
                            $qq->where('max_percentage', '>=', $percent)
                               ->orWhereNull('max_percentage');
                        })
                        ->orderByDesc('min_percentage')
                        ->first();

                    $finalTotal += $rule ? (int) $rule->score_value : 0;
                } else {
                    $finalTotal += $rawScore;
                }
            }

            $a->final_total_score = $finalTotal;
            $a->max_total_score   = $maxTotal;
        }

        // Sorting (name pakai applicants.name, total_nilai pakai final_total_score)
        $applicants = $applicants->sortBy(function ($a) use ($sort) {
            return match ($sort) {
                'name'        => mb_strtolower($a->name ?? ''),
                'section_1'   => null, // isi kalau nyimpen skor per-section sebagai field
                'section_2'   => null,
                'section_3'   => null,
                'section_4'   => null,
                'section_5'   => null,
                'total_nilai' => $a->final_total_score ?? -1,
                default       => null,
            };
        }, SORT_NATURAL, $direction === 'desc');

        // Pagination manual (karena sorting collection)
        $page = (int) request('page', 1);
        $perPage = 20;
        $applicants = new \Illuminate\Pagination\LengthAwarePaginator(
            $applicants->forPage($page, $perPage)->values(),
            $applicants->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

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

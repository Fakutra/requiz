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
use App\Services\ActivityLogger; // ✅ tambahkan ini

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

        // 🔹 ambil parameter sort & direction
        $sort      = $request->query('sort', 'name');
        $direction = $request->query('direction', 'asc');

        $allowedSorts = ['name', 'section_1', 'section_2', 'section_3', 'section_4', 'section_5', 'total_nilai'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'name';
        }

        $q = Applicant::with([
            'position',
            'batch',
            'latestEmailLog',
            'latestTestResult.sectionResults.testSection',
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
            $needle = "%".mb_strtolower($search)."%";
            $q->where(function ($w) use ($needle) {
                $w->whereRaw('LOWER(name) LIKE ?', [$needle])
                  ->orWhereRaw('LOWER(email) LIKE ?', [$needle])
                  ->orWhereRaw('LOWER(jurusan) LIKE ?', [$needle]);
            });
        }

        $applicants = $q->orderBy($sort, $direction)
            ->paginate(20)
            ->appends($request->query());

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
        // 🧩 log aktivitas export
        ActivityLogger::log(
            'export',
            'Tes Tulis',
            Auth::user()->name.' mengekspor data peserta Tes Tulis'
        );

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

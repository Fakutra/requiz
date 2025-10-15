<?php

namespace App\Http\Controllers\AdminPanel\Selection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\Batch;
use App\Models\Position;
use App\Models\TechnicalTestAnswer;
use App\Services\SelectionLogger;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TechnicalTestApplicantsExport;
use App\Services\SelectionNotifier;
use App\Services\ActivityLogger; // ✅ tambahkan ini

class TechnicalTestController extends Controller
{
    protected string $stage = 'Technical Test';

    public function index(Request $request)
    {
        $batchId    = $request->query('batch');
        $positionId = $request->query('position');
        $search     = trim((string) $request->query('search'));
        $status     = $request->query('status');

        $batches   = Batch::orderBy('id')->get();
        $positions = $batchId ? Position::where('batch_id', $batchId)->get() : collect();

        $relevantStatuses = [
            'Technical Test',
            'Interview',
            'Offering',
            'Menerima Offering',
            'Tidak Lolos Technical Test',
            'Tidak Lolos Interview',
            'Menolak Offering',
        ];

        $q = Applicant::with(['position','batch','latestEmailLog'])
            ->whereIn('status', $relevantStatuses);

        if ($batchId) $q->where('batch_id', $batchId);
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

        $applicants = $q->orderBy('name')
            ->paginate(20)
            ->appends($request->query());

        $answerRows = TechnicalTestAnswer::whereIn('applicant_id', $applicants->pluck('id'))
            ->orderBy('applicant_id')
            ->orderByDesc('submitted_at')
            ->get();

        $latestAnswers = $answerRows->unique('applicant_id')->keyBy('applicant_id');

        return view('admin.applicant.seleksi.technical-test.index', compact(
            'batches','positions','batchId','positionId','applicants','latestAnswers'
        ));
    }

    /**
     * Bulk update hasil Technical Test (lolos / tidak_lolos)
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

            $newStatus = $data['bulk_action'] === 'lolos'
                ? 'Interview'
                : 'Tidak Lolos Technical Test';

            // Log internal seleksi
            SelectionLogger::write($a, $this->stage, $data['bulk_action'], Auth::id());

            // Update status applicant
            $a->forceFill(['status' => $newStatus])->save();

            // Kirim notifikasi
            SelectionNotifier::notify($a, $this->stage, $data['bulk_action'], $newStatus);

            // ✅ Catat aktivitas admin
            ActivityLogger::log(
                $data['bulk_action'],
                'Technical Test',
                Auth::user()->name." menandai peserta {$a->name} sebagai '".strtoupper($data['bulk_action'])."' pada tahap Technical Test",
                "Applicant ID: {$a->id}"
            );
        }

        return back()->with('success', count($data['ids']).' peserta diperbarui.');
    }

    /**
     * Update nilai & keterangan untuk satu jawaban Technical Test
     */
    public function updateScore(Request $request, TechnicalTestAnswer $answer)
    {
        $data = $request->validate([
            'score'      => 'required|numeric|min:0|max:100',
            'keterangan' => 'nullable|string',
        ]);

        // Simpan data lama
        $oldData = $answer->only(['score', 'keterangan']);

        // Update nilai baru
        $answer->update([
            'score'      => $data['score'],
            'keterangan' => $data['keterangan'] ?? null,
        ]);

        // Simpan data baru
        $newData = $answer->only(['score', 'keterangan']);

        // ✅ Catat perbedaan ke log aktivitas
        \App\Services\ActivityLogger::logUpdate('Technical Test', $answer, $oldData, $newData);

        return back()->with('success', 'Nilai Technical Test berhasil disimpan.');
    }

    /**
     * Export Excel peserta Technical Test
     */
    public function export(Request $r)
    {
        // ✅ Log aktivitas export
        // ActivityLogger::log(
        //     'export',
        //     'Technical Test',
        //     Auth::user()->name.' mengekspor data peserta tahap Technical Test'
        // );

        return Excel::download(
            new TechnicalTestApplicantsExport(
                $r->query('batch'),
                $r->query('position'),
                $r->query('search'),
            ),
            'seleksi-technical-test.xlsx'
        );
    }
}

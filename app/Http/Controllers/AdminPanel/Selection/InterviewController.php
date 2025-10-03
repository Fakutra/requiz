<?php

namespace App\Http\Controllers\AdminPanel\Selection;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\InterviewResult;
use Illuminate\Http\Request;
use App\Services\SelectionLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Batch;
use App\Models\Position;

class InterviewController extends Controller
{
    protected string $stage = 'Seleksi Interview';

    public function index(Request $request)
    {
        $batchId    = $request->query('batch');
        $positionId = $request->query('position');
        $search     = trim((string) $request->query('search'));

        // Ambil semua batch & posisi (supaya bisa dipakai di filter dropdown)
        $batches   = Batch::orderBy('id')->get();
        $positions = $batchId ? Position::where('batch_id', $batchId)->get() : collect();

        // Query applicants (samakan style dengan Administrasi)
        $q = Applicant::with(['position', 'batch', 'latestEmailLog', 'myInterviewResult'])
            ->whereIn('status', [
                'Interview',
                'Offering',
                'Tidak Lolos Interview',
            ]);

        if ($batchId) {
            $q->where('batch_id', $batchId);
        }

        if ($positionId) {
            $q->where('position_id', $positionId);
        }

        if ($search !== '') {
            $needle = "%".mb_strtolower($search)."%";
            $q->where(function ($w) use ($needle) {
                $w->whereRaw('LOWER(name) LIKE ?', [$needle])
                ->orWhereRaw('LOWER(email) LIKE ?', [$needle])
                ->orWhereRaw('LOWER(jurusan) LIKE ?', [$needle]);
            });
        }

        // Urutkan, paginate, sertakan query string
        $applicants = $q->orderBy('name')
            ->paginate(20)
            ->appends($request->query());

        // Tambahkan nilai interview khusus
        foreach ($applicants as $a) {
            $a->quiz_score     = $a->testResults()->latest()->value('score');
            $a->praktik_score  = $a->technicalTestAnswers()->latest()->value('score');
            $a->interview_avg  = InterviewResult::where('applicant_id', $a->id)->avg('score');
            $a->potential_by   = InterviewResult::where('applicant_id', $a->id)
                                    ->where('potencial', true)
                                    ->with('user')
                                    ->get()
                                    ->pluck('user.name')
                                    ->toArray();
            $a->interview_note = InterviewResult::where('applicant_id', $a->id)
                                    ->orderByDesc('id')
                                    ->value('note');
        }

        return view('admin.applicant.seleksi.interview.index', compact(
            'batches', 'positions', 'batchId', 'positionId', 'applicants'
        ));
    }


    public function storeScore(Request $request)
    {
        Log::info('=== storeScore Dijalankan ===', [
            'auth_id' => auth()->id(),
            'all_input' => $request->all(),
        ]);

        // ✅ Ubah validasi: hapus "boolean" pada potencial
        $data = $request->validate([
            'applicant_id'     => 'required|exists:applicants,id',
            'poin_kepribadian' => 'required|integer|min:0|max:100',
            'poin_wawasan'     => 'required|integer|min:0|max:100',
            'poin_gestur'      => 'required|integer|min:0|max:100',
            'poin_cara_bicara' => 'required|integer|min:0|max:100',
            'note'             => 'nullable|string',
            'potencial'        => 'nullable', // ❌ jangan pakai boolean di sini
        ]);

        Log::info('=== storeScore Input Validated ===', $data);

        $total = (
            $data['poin_kepribadian'] +
            $data['poin_wawasan'] +
            $data['poin_gestur'] +
            $data['poin_cara_bicara']
        ) / 4;

        InterviewResult::updateOrCreate(
            [
                'applicant_id' => $data['applicant_id'],
                'user_id'      => auth()->id(),
            ],
            [
                'poin_kepribadian' => $data['poin_kepribadian'],
                'poin_wawasan'     => $data['poin_wawasan'],
                'poin_gestur'      => $data['poin_gestur'],
                'poin_cara_bicara' => $data['poin_cara_bicara'],
                'note'             => $data['note'],
                // ✅ di sini tetap pakai boolean agar "on" → true/1
                'potencial'        => $request->boolean('potencial'),
                'score'            => $total,
            ]
        );

        return back()->with('success', 'Penilaian interview berhasil disimpan.');
    }

    public function bulkMark(Request $r)
    {
        $data = $r->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:applicants,id',
            'bulk_action' => 'required|in:lolos,tidak_lolos',
        ]);

        foreach ($data['ids'] as $id) {
            $a = Applicant::find($id);
            SelectionLogger::write($a, $this->stage, $data['bulk_action'], auth()->id());
            $a->forceFill([
                'status' => $this->newStatus($data['bulk_action'], $a->status)
            ])->save();
        }

        return back()->with('success', count($data['ids']).' peserta diperbarui.');
    }


    private function newStatus(string $result, string $current): string
    {
        if ($result === 'lolos') {
            return 'Offering'; // tahap setelah Interview
        }
        return 'Tidak Lolos Interview';
    }
}

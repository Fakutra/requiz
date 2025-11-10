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
use App\Services\ActivityLogger; // ✅

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

        // sorting
        $sort      = $request->query('sort', 'name');
        $direction = $request->query('direction', 'asc');
        $allowedSorts = ['name', 'email', 'pdf', 'keterangan', 'score'];
        if (!in_array($sort, $allowedSorts, true)) $sort = 'name';

        // status yang relevan di tahap ini
        $relevantStatuses = [
            'Technical Test',
            'Interview',
            'Offering',
            'Menerima Offering',
            'Tidak Lolos Technical Test',
            'Tidak Lolos Interview',
            'Menolak Offering',
        ];

        // ⚙️ Query utama — langsung dari applicants (tanpa relasi user/profile)
        $q = Applicant::with(['position','batch','latestEmailLog'])
            ->whereIn('status', $relevantStatuses);

        if ($batchId)    $q->where('batch_id', $batchId);
        if ($positionId) $q->where('position_id', $positionId);
        if ($status)     $q->where('status', $status);

        if ($search !== '') {
            $needle = "%".mb_strtolower($search)."%";
            $q->where(function ($w) use ($needle) {
                $w->whereRaw('LOWER(name) LIKE ?', [$needle])
                  ->orWhereRaw('LOWER(email) LIKE ?', [$needle])
                  ->orWhereRaw('LOWER(jurusan) LIKE ?', [$needle])
                  ->orWhereHas('position', fn($p) =>
                      $p->whereRaw('LOWER(name) LIKE ?', [$needle])
                  );
            });
        }

        // ambil applicants dulu
        $applicants = $q->get();

        // ambil jawaban terakhir per applicant
        $answerRows = TechnicalTestAnswer::whereIn('applicant_id', $applicants->pluck('id'))
            ->orderBy('applicant_id')
            ->orderByDesc('submitted_at')
            ->get();

        $latestAnswers = $answerRows->unique('applicant_id')->keyBy('applicant_id');

        // inject kolom virtual: pdf (1/0), keterangan, score
        $applicants = $applicants->map(function ($a) use ($latestAnswers) {
            $ans = $latestAnswers[$a->id] ?? null;
            $a->pdf        = ($ans && $ans->answer_url) ? 1 : 0;
            $a->keterangan = $ans?->keterangan ?? null;
            $a->score      = $ans?->score ?? null;
            return $a;
        });

        // sorting di collection (name/email/pdf/keterangan/score tersedia di object)
        $applicants = $applicants->sortBy($sort, SORT_NATURAL, $direction === 'desc');

        // pagination manual
        $page    = (int) $request->query('page', 1);
        $perPage = 20;
        $applicants = new \Illuminate\Pagination\LengthAwarePaginator(
            $applicants->forPage($page, $perPage)->values(),
            $applicants->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.applicant.seleksi.technical-test.index', compact(
            'batches', 'positions', 'batchId', 'positionId', 'applicants', 'latestAnswers'
        ));
    }

    /**
     * Bulk update hasil Technical Test (lolos / tidak_lolos)
     */
    public function bulkMark(Request $r)
    {
        $data = $r->validate([
            'ids'         => 'required|array',
            'ids.*'       => 'exists:applicants,id',
            'bulk_action' => 'required|in:lolos,tidak_lolos',
        ]);

        foreach ($data['ids'] as $id) {
            $a = Applicant::find($id);

            $newStatus = $data['bulk_action'] === 'lolos'
                ? 'Interview'
                : 'Tidak Lolos Technical Test';

            // log internal
            SelectionLogger::write($a, $this->stage, $data['bulk_action'], Auth::id());

            // update status
            $a->forceFill(['status' => $newStatus])->save();

            // notifikasi ke kandidat
            SelectionNotifier::notify($a, $this->stage, $data['bulk_action'], $newStatus);

            // aktivitas admin
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

        // simpan before/after
        $oldData = $answer->only(['score', 'keterangan']);

        $answer->update([
            'score'      => $data['score'],
            'keterangan' => $data['keterangan'] ?? null,
        ]);

        $newData = $answer->only(['score', 'keterangan']);

        // log diff
        ActivityLogger::logUpdate('Technical Test', $answer, $oldData, $newData);

        return back()->with('success', 'Nilai Technical Test berhasil disimpan.');
    }

    /**
     * Export Excel peserta Technical Test
     */
    public function export(Request $r)
    {
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

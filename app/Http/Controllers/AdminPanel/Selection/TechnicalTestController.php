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
use App\Services\ActivityLogger;
use Throwable;

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

        $sort      = $request->query('sort', 'name');
        $direction = $request->query('direction', 'asc');
        $allowedSorts = ['name', 'email', 'pdf', 'keterangan', 'score'];
        if (!in_array($sort, $allowedSorts, true)) $sort = 'name';

        $relevantStatuses = [
            'Technical Test',
            'Interview',
            'Offering',
            'Menerima Offering',
            'Tidak Lolos Technical Test',
            'Tidak Lolos Interview',
            'Menolak Offering',
        ];

        try {
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

            $applicants = $q->get();

            $answerRows = TechnicalTestAnswer::whereIn('applicant_id', $applicants->pluck('id'))
                ->orderBy('applicant_id')
                ->orderByDesc('submitted_at')
                ->get();

            $latestAnswers = $answerRows->unique('applicant_id')->keyBy('applicant_id');

            $applicants = $applicants->map(function ($a) use ($latestAnswers) {
                $ans = $latestAnswers[$a->id] ?? null;
                $a->pdf        = ($ans && $ans->answer_url) ? 1 : 0;
                $a->keterangan = $ans?->keterangan ?? null;
                $a->score      = $ans?->score ?? null;
                return $a;
            });

            $applicants = $applicants->sortBy($sort, SORT_NATURAL, $direction === 'desc');

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
        } catch (Throwable $e) {
            report($e);

            ActivityLogger::log(
                'error',
                'Technical Test',
                (Auth::user()?->name ?? 'System').' gagal memuat data Technical Test',
                $e->getMessage()
            );

            return back()->with('error', 'Gagal memuat data Technical Test. Coba lagi atau cek log server.');
        }
    }

    public function bulkMark(Request $r)
    {
        $data = $r->validate([
            'ids'         => 'required|array',
            'ids.*'       => 'exists:applicants,id',
            'bulk_action' => 'required|in:lolos,tidak_lolos',
        ]);

        $success = 0;
        $failed  = 0;
        $failedNames = [];

        foreach ($data['ids'] as $id) {
            $a = Applicant::find($id);
            if (!$a) {
                $failed++;
                $failedNames[] = "#{$id}";
                continue;
            }

            // 🔒 FINAL LOCK (SAMA DENGAN ADMIN & TES TULIS)
            $finalTechStatuses = [
                'Interview',
                'Offering',
                'Menerima Offering',
                'Tidak Lolos Technical Test',
                'Tidak Lolos Interview',
                'Menolak Offering',
            ];

            $emailLocked = $a->latestEmailLog
                && $a->latestEmailLog->stage === $this->stage
                && $a->latestEmailLog->success;

            if (in_array($a->status, $finalTechStatuses, true) && $emailLocked) {
                $failed++;
                $failedNames[] = $a->name . ' (sudah final & email terkirim)';
                continue;
            }

            try {
                $newStatus = $data['bulk_action'] === 'lolos'
                    ? 'Interview'
                    : 'Tidak Lolos Technical Test';

                SelectionLogger::write($a, $this->stage, $data['bulk_action'], Auth::id());

                $a->forceFill(['status' => $newStatus])->save();

                SelectionNotifier::notify($a, $this->stage, $data['bulk_action'], $newStatus);

                ActivityLogger::log(
                    $data['bulk_action'],
                    'Technical Test',
                    Auth::user()->name." menandai {$a->name}",
                    "Applicant ID: {$a->id}"
                );

                $success++;
            } catch (Throwable $e) {
                report($e);
                $failed++;
                $failedNames[] = $a->name;
            }
        }

        $resp = back();

        if ($success) {
            $resp = $resp->with('success', "{$success} peserta berhasil diproses.");
        }

        if ($failed) {
            $resp = $resp->with(
                'error',
                "Ada {$failed} peserta gagal diproses: ".implode(', ', array_slice($failedNames, 0, 10))
            );
        }

        return $resp;
    }

    public function updateScore(Request $request, TechnicalTestAnswer $answer)
    {
        $data = $request->validate([
            'score'      => 'required|numeric|min:0|max:100',
            'keterangan' => 'nullable|string',
        ]);

        $oldData = $answer->only(['score', 'keterangan']);

        try {
            $answer->update([
                'score'      => $data['score'],
                'keterangan' => $data['keterangan'] ?? null,
            ]);

            $newData = $answer->only(['score', 'keterangan']);

            ActivityLogger::logUpdate('Technical Test', $answer, $oldData, $newData);

            return back()->with('success', 'Nilai Technical Test berhasil disimpan.');
        } catch (Throwable $e) {
            report($e);

            ActivityLogger::log(
                'error',
                'Technical Test',
                (Auth::user()?->name ?? 'System').' GAGAL menyimpan nilai Technical Test',
                $e->getMessage()
            );

            return back()->with('error', 'Gagal menyimpan nilai Technical Test. Coba lagi atau cek log server.');
        }
    }

    public function export(Request $r)
    {
        try {
            return Excel::download(
                new TechnicalTestApplicantsExport(
                    $r->query('batch'),
                    $r->query('position'),
                    $r->query('search'),
                ),
                'seleksi-technical-test.xlsx'
            );
        } catch (Throwable $e) {
            report($e);

            ActivityLogger::log(
                'error',
                'Technical Test',
                (Auth::user()?->name ?? 'System').' GAGAL export peserta Technical Test',
                $e->getMessage()
            );

            return back()->with('error', 'Gagal export Excel Technical Test. Coba lagi atau cek log server.');
        }
    }
}

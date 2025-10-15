<?php

namespace App\Http\Controllers\AdminPanel\Selection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\Batch;
use App\Models\Position;
use App\Models\Offering;
use App\Models\Division;
use App\Models\Job;
use App\Models\Placement;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth; // ✅ tambahkan
use Illuminate\Validation\ValidationException;
use App\Exports\OfferingApplicantsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\ActivityLogger; // ✅ tambahkan

class OfferingController extends Controller
{
    protected string $stage = 'Offering';

    public function index(Request $request)
    {
        $batchId    = $request->query('batch');
        $positionId = $request->query('position');
        $search     = trim((string) $request->query('search'));
        $jurusan    = trim((string) $request->query('jurusan'));

        $allJurusan = Applicant::select('jurusan')
            ->whereNotNull('jurusan')
            ->distinct()
            ->orderBy('jurusan')
            ->pluck('jurusan');

        $batches   = Batch::orderBy('id')->get();
        $positions = $batchId ? Position::where('batch_id', $batchId)->get() : Position::all();

        $q = Applicant::with([
                'position', 
                'batch', 
                'latestEmailLog',
                'offering.division',
                'offering.job',
                'offering.placement',
                'interviewResults.user'
            ])
            ->whereIn('status', [
                'Offering',
                'Menerima Offering',
                'Menolak Offering',
            ]);

        if ($batchId) {
            $q->where('batch_id', $batchId);
        }
        if ($positionId) {
            $q->where('position_id', $positionId);
        }
        if ($search !== '') {
            $q->search($search);
        }
        if ($jurusan !== '') {
            $q->whereRaw('LOWER(jurusan) LIKE ?', ['%'.mb_strtolower($jurusan).'%']);
        }

        $applicants = $q->orderBy('name')
            ->paginate(20)
            ->appends($request->query());

        // master untuk dropdown
        $divisions  = Division::orderBy('name')->get();
        $jobs       = Job::orderBy('name')->get();
        $placements = Placement::orderBy('name')->get();

        return view('admin.applicant.seleksi.offering.index', compact(
            'batches','positions','batchId','positionId',
            'applicants','jurusan','allJurusan',
            'divisions','jobs','placements'
        ));
    }

    /**
     * EXPORT DATA OFFERING
     */
    public function export(Request $request)
    {
        $batchId    = $request->query('batch');
        $positionId = $request->query('position');
        $search     = $request->query('search');
        $jurusan    = $request->query('jurusan');

        $fileName = 'Offering_Applicants_' . now()->format('Ymd_His') . '.xlsx';

        // ✅ Log aktivitas export
        try {
            $user = Auth::user()?->name ?? 'System';
            $filters = collect([
                $batchId ? "Batch ID {$batchId}" : "Semua Batch",
                $positionId ? "Posisi ID {$positionId}" : "Semua Posisi",
                $jurusan ? "Jurusan: {$jurusan}" : "Semua Jurusan"
            ])->implode(', ');

            // ActivityLogger::log(
            //     'export',
            //     'Offering',
            //     "{$user} mengekspor data peserta Offering ({$filters})"
            // );
        } catch (\Throwable $e) {
            Log::warning('Gagal mencatat log export Offering: '.$e->getMessage());
        }

        return Excel::download(
            new OfferingApplicantsExport($batchId, $positionId, $search, $jurusan),
            $fileName
        );
    }

    /**
     * CREATE / UPDATE OFFERING DATA
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'applicant_id'     => 'required|exists:applicants,id',
            'position'         => 'nullable|string',
            'division_id'      => 'nullable|exists:divisions,id',
            'job_id'           => 'nullable|exists:jobs,id',
            'placement_id'     => 'nullable|exists:placements,id',
            'gaji'             => 'nullable|numeric',
            'uang_makan'       => 'nullable|numeric',
            'uang_transport'   => 'nullable|numeric',
            'kontrak_mulai'    => 'nullable|date',
            'kontrak_selesai'  => 'nullable|date|after_or_equal:kontrak_mulai',
            'link_pkwt'        => 'nullable|string',
            'link_berkas'      => 'nullable|string',
            'link_form_pelamar'=> 'nullable|string',
        ]);

        try {
            $applicant = Applicant::findOrFail($data['applicant_id']);

            $existingOffering = $applicant->offering()->first();
            $oldData = $existingOffering ? $existingOffering->toArray() : [];

            // ✅ Simpan atau update Offering
            $offering = $applicant->offering()->updateOrCreate(
                ['applicant_id' => $applicant->id],
                $data
            );

            // ✅ Update status applicant
            $applicant->update(['status' => 'Offering']);

            // ✅ Log aktivitas
            $user = Auth::user()?->name ?? 'System';
            $desc = $existingOffering
                ? "{$user} memperbarui data Offering untuk {$applicant->name}"
                : "{$user} membuat data Offering untuk {$applicant->name}";

            ActivityLogger::log(
                $existingOffering ? 'update' : 'create',
                'Offering',
                $desc,
                json_encode([
                    'Sebelumnya' => $oldData,
                    'Sesudahnya' => $offering->toArray(),
                ])
            );

            return back()->with('success', 'Data Offering berhasil disimpan.');
        } catch (\Throwable $e) {
            Log::error('Gagal menyimpan Offering: '.$e->getMessage());
            return back()->with('error', 'Gagal menyimpan data Offering.');
        }
    }

    /**
     * BULK MARK (MENERIMA / MENOLAK OFFERING)
     */
    public function bulkMark(Request $request)
    {
        if (!$request->has('ids')) {
            return back();
        }

        $ids = $request->input('ids', []);
        $action = $request->input('bulk_action');

        if (!empty($ids)) {
            $status = $action === 'accepted' ? 'Menerima Offering' : 'Menolak Offering';
            Applicant::whereIn('id', $ids)->update(['status' => $status]);

            // ✅ Log aktivitas
            $user = Auth::user()?->name ?? 'System';
            $actionLabel = $status === 'Menerima Offering' ? 'menerima' : 'menolak';
            $count = count($ids);

            ActivityLogger::log(
                $action,
                'Offering',
                "{$user} menandai {$count} peserta sebagai {$actionLabel} Offering",
                "Jumlah Peserta: {$count}"
            );

            return back()->with('success', 'Status peserta berhasil diperbarui.');
        }

        return back();
    }
}





// public function update(Request $request, $id)
    // {
    //     try {
    //         $data = $request->validate([
    //             'position'          => 'nullable|string',
    //             'division_id'       => 'nullable|exists:divisions,id',
    //             'job_id'            => 'nullable|exists:jobs,id',
    //             'placement_id'      => 'nullable|exists:placements,id',
    //             'gaji'              => 'nullable|numeric',
    //             'uang_makan'        => 'nullable|numeric',
    //             'uang_transport'    => 'nullable|numeric',
    //             'kontrak_mulai'     => 'nullable|date',
    //             'kontrak_selesai'   => 'nullable|date|after_or_equal:kontrak_mulai',
    //             'link_pkwt'         => 'nullable|string',
    //             'link_berkas'       => 'nullable|string',
    //             'link_form_pelamar' => 'nullable|string',
    //         ]);

    //         array_walk($data, fn (&$v) => $v === '' ? $v = null : $v);
    //         foreach (['link_pkwt','link_berkas','link_form_pelamar'] as $k) {
    //             if (!empty($data[$k]) && !preg_match('~^https?://~i', $data[$k])) {
    //                 $data[$k] = 'https://' . ltrim($data[$k]);
    //             }
    //         }

    //         $applicant = Applicant::findOrFail($id);
    //         $offering = $applicant->offering ?? new Offering(['applicant_id' => $applicant->id]);
    //         $offering->fill($data);
    //         $offering->save();

    //         $applicant->status = 'Offering';
    //         $applicant->save();

    //         return back()->with('success', 'Detail offering berhasil disimpan.');
    //     } catch (\Throwable $e) {
    //         Log::error('Gagal update Offering', ['error' => $e->getMessage()]);
    //         return back()->with('error', $e->getMessage())->withInput();
    //     }
    // }
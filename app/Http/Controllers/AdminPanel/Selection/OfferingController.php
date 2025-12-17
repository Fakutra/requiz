<?php

namespace App\Http\Controllers\AdminPanel\Selection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\Batch;
use App\Models\Position;
use App\Models\Offering;
use App\Models\Field;
use App\Models\SubField;
use App\Models\Job;
use App\Models\Placement;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth; // ✅ tambahkan
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use App\Exports\OfferingApplicantsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\ActivityLogger; // ✅ tambahkan
use Illuminate\Support\Facades\DB;

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
                'offering.field',
                'offering.subfield',
                'offering.job',
                'offering.placement',
                'interviewResults.user',
                'pickedBy',
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

        // Ambil parameter sorting
        $sort      = $request->query('sort', 'name');
        $direction = $request->query('direction', 'asc');

        // Kolom yang boleh di-sort
        $allowedSorts = ['name', 'email', 'posisi', 'penempatan', 'jabatan', 'bidang', 'subbidang', 'status'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'name';
        }

        // Ambil semua data dulu (tanpa orderBy)
        $applicants = $q->get()->map(function ($a) {
            $a->posisi     = $a->position?->name;
            $a->penempatan = $a->offering?->placement?->name;
            $a->jabatan    = $a->offering?->job?->name;
            $a->bidang     = $a->offering?->field?->name;
            $a->subbidang     = $a->offering?->subfield?->name;
            return $a;
        });

        // Sorting collection
        $applicants = $applicants->sortBy($sort, SORT_NATURAL, $direction === 'desc');

        // Pagination manual
        $page = request('page', 1);
        $perPage = 20;
        $applicants = new \Illuminate\Pagination\LengthAwarePaginator(
            $applicants->forPage($page, $perPage),
            $applicants->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );


        // master untuk dropdown
        $fields  = Field::orderBy('name')->get();
        $subfields  = SubField::orderBy('name')->get();
        $jobs       = Job::orderBy('name')->get();
        $placements = Placement::orderBy('name')->get();

        return view('admin.applicant.seleksi.offering.index', compact(
            'batches','positions','batchId','positionId',
            'applicants','jurusan','allJurusan',
            'fields','subfields','jobs','placements'
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
        try {
            $data = $request->validate([
                'applicant_id'      => 'required|exists:applicants,id',
                'field_id'          => 'required|exists:fields,id',
                'sub_field_id'      => 'required|exists:sub_fields,id',
                'job_id'            => 'required|exists:jobs,id',
                'placement_id'      => 'required|exists:placements,id',
                'gaji'              => 'required|numeric',
                'uang_makan'        => 'required|numeric',
                'uang_transport'    => 'required|numeric',
                'kontrak_mulai'     => 'required|date',
                'kontrak_selesai'   => 'required|date|after_or_equal:kontrak_mulai',
                'link_pkwt'         => 'required|string',
                'link_berkas'       => 'required|string',
                'link_form_pelamar' => 'required|string',
            ]);

            $offering = DB::transaction(function () use ($data) {
                $applicant = Applicant::findOrFail($data['applicant_id']);
                $payload   = Arr::except($data, ['applicant_id']);

                $offering = $applicant->offering()->updateOrCreate(
                    ['applicant_id' => $applicant->id],
                    $payload
                );

                $applicant->update(['status' => 'Offering']);

                return $offering;
            });

            // ✅ Logger jangan boleh bikin gagal simpan
            try {
                // Kalau method log lo cuma nerima 3 argumen, cukup pakai ini:
                ActivityLogger::log(
                    'save',
                    'Offering',
                    'Menyimpan offering untuk applicant_id: ' . $data['applicant_id']
                );

                // Kalau ternyata log lo support detail, baru tambahin, tapi tetap di try-catch
                // ActivityLogger::log('save','Offering','Menyimpan offering...', $offering->toArray());
            } catch (\Throwable $e) {
                Log::warning('Gagal mencatat ActivityLogger Offering: '.$e->getMessage());
            }

            return back()->with('success', 'Data Offering berhasil disimpan.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();

        } catch (QueryException $e) {
            Log::error('Offering DB Error', [
                'error' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
            ]);

            return back()->with('error', 'Gagal menyimpan Offering (Database Error). Silakan hubungi admin.');

        } catch (\Throwable $e) {
            Log::error('Offering General Error', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);

            return back()->with('error', 'Terjadi kesalahan sistem saat menyimpan Offering.');
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

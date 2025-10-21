<?php

namespace App\Http\Controllers\AdminPanel\Selection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\Batch;
use App\Models\Position;
use App\Services\SelectionLogger;
use Illuminate\Support\Facades\Auth;
use App\Exports\AdministrasiApplicantsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\SelectionNotifier;
use App\Services\ActivityLogger; // ✅ tambahkan ini

class AdministrasiController extends Controller
{
    protected string $stage = 'Seleksi Administrasi';

    /**
     * Halaman daftar peserta tahap Administrasi
     */
    public function index(Request $request)
    {
        $batchId    = $request->query('batch');
        $positionId = $request->query('position');
        $search     = trim((string) $request->query('search'));
        $jurusan    = trim((string) $request->query('jurusan'));

        // 🔹 ambil parameter sort & direction dari query
        $sort      = $request->query('sort', 'name');       // default sort by name
        $direction = $request->query('direction', 'asc');   // default asc

        // whitelist kolom yang boleh di-sort
        $allowedSorts = ['name', 'email', 'jurusan', 'position_id', 'age'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'name'; // fallback
        }

        $allJurusan = Applicant::select('jurusan')
            ->distinct()
            ->orderBy('jurusan')
            ->pluck('jurusan');

        $batches   = Batch::orderBy('id')->get();
        $positions = $batchId ? Position::where('batch_id', $batchId)->get() : Position::all();

        // 🔹 ambil semua peserta di batch
        $q = Applicant::with(['position', 'batch', 'latestEmailLog']);

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

        if ($jurusan !== '') {
            $q->whereRaw('LOWER(jurusan) LIKE ?', ['%'.mb_strtolower($jurusan).'%']);
        }

        // 🔹 tambahkan orderBy dinamis
        $applicants = $q->get()->map(function ($a) {
                $a->age = $a->birth_date ? \Carbon\Carbon::parse($a->birth_date)->age : null;
                return $a;
            });

        if ($sort === 'age') {
            $applicants = $applicants->sortBy('age', SORT_REGULAR, $direction === 'desc');
        } else {
            $applicants = $applicants->sortBy($sort, SORT_REGULAR, $direction === 'desc');
        }

        // Pagination manual (opsional)
        $page = request('page', 1);
        $perPage = 20;
        $applicants = new \Illuminate\Pagination\LengthAwarePaginator(
            $applicants->forPage($page, $perPage),
            $applicants->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );


        return view('admin.applicant.seleksi.administrasi.index', compact(
            'batches', 'positions', 'batchId', 'positionId',
            'applicants', 'jurusan', 'allJurusan',
            'sort', 'direction'
        ));
    }

    /**
     * Bulk update hasil seleksi (lolos / gagal)
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

            // 1️⃣ log internal seleksi
            SelectionLogger::write($a, $this->stage, $data['bulk_action'], Auth::id());

            // 2️⃣ update status pelamar
            $newStatus = $this->newStatus($data['bulk_action'], $a->status);
            $a->forceFill(['status' => $newStatus])->save();

            // 3️⃣ kirim notifikasi ke peserta
            SelectionNotifier::notify(
                $a,
                $this->stage,
                $data['bulk_action'],
                $newStatus
            );

            // 4️⃣ catat log aktivitas admin (ActivityLogger)
            ActivityLogger::log(
                $data['bulk_action'],                  // action (lolos / tidak_lolos)
                'Seleksi Administrasi',                // module
                Auth::user()->name." menandai peserta {$a->name} sebagai '".strtoupper($data['bulk_action'])."'", // description
                "Applicant ID: {$a->id}"               // target
            );
        }

        return back()->with('success', count($data['ids']).' peserta diperbarui.');
    }

    /**
     * Tentukan status baru applicant berdasarkan hasil
     */
    private function newStatus(string $result, string $current): string
    {
        if ($result === 'lolos') {
            return 'Tes Tulis';
        }
        return 'Tidak Lolos Seleksi Administrasi';
    }

    /**
     * Export Excel khusus tahap Administrasi
     */
    public function export(Request $r)
    {
        // 🧩 log aktivitas export
        // ActivityLogger::log(
        //     'export',
        //     'Seleksi Administrasi',
        //     Auth::user()->name.' mengekspor data peserta seleksi administrasi'
        // );

        return Excel::download(
            new AdministrasiApplicantsExport(
                $r->query('batch'),
                $r->query('position'),
                $r->query('search'),
            ),
            'seleksi-administrasi.xlsx'
        );
    }

    /**
     * Simpan selected IDs di session (kirim email massal)
     */
    public function setSelectedIds(Request $r)
    {
        $data = $r->validate([
            'ids' => 'required|string', // daftar id dipisahkan koma
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        $ids = explode(',', $data['ids']);
        $applicants = Applicant::whereIn('id', $ids)->get();

        foreach ($applicants as $a) {
            // Mail::to($a->email)->send(new SeleksiEmail(...));
        }

        // 🧩 catat log aktivitas pengiriman email
        ActivityLogger::log(
            'send_email',
            'Seleksi Administrasi',
            Auth::user()->name.' mengirim email ke '.count($applicants).' peserta',
            implode(',', $ids)
        );

        return back()->with('success', 'Email terkirim ke '.count($applicants).' peserta terpilih.');
    }
}

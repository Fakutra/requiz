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
use App\Services\ActivityLogger;

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
        $status     = trim((string) $request->query('status'));

        // ✅ sort langsung via applicants.* atau via accessor age
        $sort      = $request->query('sort', 'name');
        $direction = $request->query('direction', 'asc');
        $allowedSorts = ['name', 'email', 'jurusan', 'position_id', 'age'];
        if (!in_array($sort, $allowedSorts, true)) $sort = 'name';

        $allJurusan = Applicant::select('jurusan')->distinct()->orderBy('jurusan')->pluck('jurusan');

        $batches   = Batch::orderBy('id')->get();
        $positions = $batchId ? Position::where('batch_id', $batchId)->get() : Position::all();

        $q = Applicant::query()
            ->with([
                'position:id,name',
                'batch:id,name',
                'latestEmailLog',
            ]);

        if ($batchId)    $q->where('batch_id', $batchId);
        if ($positionId) $q->where('position_id', $positionId);

        // 🔎 search: applicants.name, applicants.email, applicants.jurusan, positions.name
        if ($search !== '') {
            $needle = '%'.mb_strtolower($search).'%';
            $q->where(function ($w) use ($needle) {
                $w->whereRaw('LOWER(applicants.name) LIKE ?', [$needle])
                  ->orWhereRaw('LOWER(applicants.email) LIKE ?', [$needle])
                  ->orWhereRaw('LOWER(applicants.jurusan) LIKE ?', [$needle])
                  ->orWhereHas('position', fn($p) =>
                      $p->whereRaw('LOWER(name) LIKE ?', [$needle])
                  );
            });
        }

        if ($jurusan !== '') {
            $q->whereRaw('LOWER(applicants.jurusan) LIKE ?', ['%'.mb_strtolower($jurusan).'%']);
        }

        // 🟨 filter status opsional (tetep sama)
        if ($status !== '') {
            if ($status === 'Seleksi Administrasi') {
                $q->where('status', 'Seleksi Administrasi');
            } elseif ($status === 'Tes Tulis') {
                $q->whereIn('status', [
                    'Tes Tulis','Technical Test','Interview','Offering',
                    'Menerima Offering','Tidak Lolos Tes Tulis',
                    'Tidak Lolos Technical Test','Tidak Lolos Interview','Menolak Offering'
                ]);
            } elseif ($status === 'Tidak Lolos Seleksi Administrasi') {
                $q->where('status', 'Tidak Lolos Seleksi Administrasi');
            }
        }

        // ===== Sorting =====
        // name/email: bisa order di DB
        // jurusan/position_id: juga bisa di DB
        // age: perlu accessor → sort di collection
        if ($sort === 'age') {
            $collection = $q->get()->sortBy(fn ($a) => $a->age ?? -1, SORT_NATURAL, $direction === 'desc');

            $page    = (int) $request->input('page', 1);
            $perPage = 20;
            $applicants = new \Illuminate\Pagination\LengthAwarePaginator(
                $collection->forPage($page, $perPage)->values(),
                $collection->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            // map kolom sort → kolom DB
            $orderCol = match ($sort) {
                'name'        => 'applicants.name',
                'email'       => 'applicants.email',
                'jurusan'     => 'applicants.jurusan',
                'position_id' => 'applicants.position_id',
                default       => 'applicants.id',
            };

            $applicants = $q->orderBy($orderCol, $direction)
                ->paginate(20)
                ->appends($request->query());
        }

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
            'ids'         => 'required|array',
            'ids.*'       => 'exists:applicants,id',
            'bulk_action' => 'required|in:lolos,tidak_lolos',
        ]);

        foreach ($data['ids'] as $id) {
            $a = Applicant::find($id);

            // 1️⃣ log internal seleksi
            SelectionLogger::write($a, $this->stage, $data['bulk_action'], Auth::id());

            // 2️⃣ update status pelamar
            $newStatus = $this->newStatus($data['bulk_action'], (string) $a->status);
            $a->forceFill(['status' => $newStatus])->save();

            // 3️⃣ kirim notifikasi ke peserta
            SelectionNotifier::notify(
                $a,
                $this->stage,
                $data['bulk_action'],
                $newStatus
            );

            // 4️⃣ Activity log (pakai kolom applicants.name)
            ActivityLogger::log(
                $data['bulk_action'],
                'Seleksi Administrasi',
                Auth::user()->name." menandai peserta {$a->name} sebagai '".strtoupper($data['bulk_action'])."'",
                "Applicant ID: {$a->id}"
            );
        }

        return back()->with('success', 'Status '.count($data['ids']).' peserta diperbarui.');
    }

    /**
     * Tentukan status baru applicant berdasarkan hasil
     */
    private function newStatus(string $result, string $current): string
    {
        return $result === 'lolos'
            ? 'Tes Tulis'
            : 'Tidak Lolos Seleksi Administrasi';
    }

    /**
     * Export Excel khusus tahap Administrasi
     */
    public function export(Request $r)
    {
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
            'ids'     => 'required|string', // daftar id dipisahkan koma
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        $ids = array_filter(array_map('trim', explode(',', $data['ids'])));
        $applicants = Applicant::whereIn('id', $ids)->get();

        // Contoh kirim email (diaktifin sesuai implementation lu)
        // foreach ($applicants as $a) {
        //     Mail::to($a->email)->send(new SeleksiEmail(
        //         subject: $data['subject'],
        //         message: $data['message'],
        //         applicant: $a
        //     ));
        // }

        ActivityLogger::log(
            'send_email',
            'Seleksi Administrasi',
            Auth::user()->name.' mengirim email ke '.count($applicants).' peserta',
            implode(',', $ids)
        );

        return back()->with('success', 'Email terkirim ke '.count($applicants).' peserta terpilih.');
    }
}

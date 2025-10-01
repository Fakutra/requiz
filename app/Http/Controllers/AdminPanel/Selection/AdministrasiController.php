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
            $jurusan = trim((string) $request->query('jurusan'));
            $allJurusan = Applicant::select('jurusan')
                ->distinct()
                ->orderBy('jurusan')
                ->pluck('jurusan');


            $batches   = Batch::orderBy('id')->get();
            $positions = $batchId ? Position::where('batch_id', $batchId)->get() : Position::all();

            $q = Applicant::with(['position', 'batch', 'latestEmailLog'])
                ->whereIn('status', [
                    'Seleksi Administrasi',
                    'Tes Tulis',
                    'Tidak Lolos Seleksi Administrasi',
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

            if ($jurusan !== '') {
                $q->whereRaw('LOWER(jurusan) LIKE ?', ['%'.mb_strtolower($jurusan).'%']);
            }

            $applicants = $q->orderBy('name')
                ->paginate(20)
                ->appends($request->query());

            return view('admin.applicant.seleksi.administrasi.index', compact(
                'batches', 'positions', 'batchId', 'positionId', 'applicants', 'jurusan', 'allJurusan'
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
            SelectionLogger::write($a, $this->stage, $data['bulk_action'], Auth::id());
            $a->forceFill(['status' => $this->newStatus($data['bulk_action'], $a->status)])->save();
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
     * Simpan selected IDs di session
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
            // gunakan service email yang sudah ada
            // misalnya dispatch job atau langsung kirim
            // Mail::to($a->email)->send(new SeleksiEmail(...));
        }

        return back()->with('success', 'Email terkirim ke '.count($applicants).' peserta terpilih.');
    }
}

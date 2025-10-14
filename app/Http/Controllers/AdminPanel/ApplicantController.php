<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\Position;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Exports\ApplicantsExport;
use Maatwebsite\Excel\Facades\Excel;

class ApplicantController extends Controller
{
    /**
     * INDEX: search + filter batch + filter position (DB-agnostic)
     */
    public function index(Request $request)
    {
        $search     = trim((string) $request->query('search'));
        $positionId = $request->query('position');
        $batchId    = $request->query('batch');

        $q = Applicant::query()->with(['position','batch']);

        // Pencarian aman untuk MySQL & Postgres
        if ($search !== '') {
            $needle = '%'.mb_strtolower($search).'%';
            $q->where(function ($w) use ($needle) {
                $w->whereRaw('LOWER(name) LIKE ?', [$needle])
                  ->orWhereRaw('LOWER(email) LIKE ?', [$needle])
                  ->orWhereRaw('LOWER(jurusan) LIKE ?', [$needle])
                  ->orWhereHas('position', fn($p) =>
                      $p->whereRaw('LOWER(name) LIKE ?', [$needle])
                  );
            });
        }

        // Filter
        if (!empty($positionId)) {
            $q->where('position_id', $positionId);
        }
        if (!empty($batchId)) {
            $q->where('batch_id', $batchId);
        }

        $applicants = $q->orderBy('name')->paginate(15)->withQueryString();

        // Dropdown data
        $positions  = Position::orderBy('name')->get(['id','name']);
        $batches    = Batch::orderBy('id')->get(['id','name']);

        return view('admin.applicant.index', compact('applicants','positions','batches'));
    }

    /**
     * UPDATE: dipanggil dari modal Edit
     */
    public function update(Request $request, Applicant $applicant)
    {
        $allowedPendidikan = ['SMA/Sederajat','Diploma','S1','S2','S3'];
        $allowedStatus = [
            'Seleksi Administrasi','Tes Tulis','Technical Test','Interview','Offering',
            'Tidak Lolos Seleksi Administrasi','Tidak Lolos Seleksi Tes Tulis',
            'Tidak Lolos Technical Test','Tidak Lolos Interview',
            'Menerima Offering','Menolak Offering',
        ];

        $data = $request->validate([
            'name'        => ['required','string','max:255'],
            'email'       => ['required','email','max:255', Rule::unique('applicants','email')->ignore($applicant->id)],
            'nik'         => ['nullable','string','max:16'],
            'no_telp'     => ['nullable','string','max:20'],
            'tpt_lahir'   => ['nullable','string','max:255'],
            'tgl_lahir'   => ['nullable','date'],
            'alamat'      => ['nullable','string','max:500'],
            'pendidikan'  => ['nullable', Rule::in($allowedPendidikan)],
            'universitas' => ['nullable','string','max:255'],
            'jurusan'     => ['nullable','string','max:255'],
            'thn_lulus'   => ['nullable','digits:4'],
            'position_id' => ['required','exists:positions,id'],
            'batch_id'    => ['nullable','exists:batches,id'],
            'status'      => ['nullable', Rule::in($allowedStatus)],
            'ekspektasi_gaji'   => 'required|numeric|min:0|max:100000000',
            'skills'      => ['nullable','string','max:5000'],
            'cv_document' => ['nullable','file','mimes:pdf','max:3072'], // 3 MB
        ]);

        // Handle CV baru
        if ($request->hasFile('cv_document')) {
            if ($applicant->cv_document) {
                Storage::disk('public')->delete($applicant->cv_document);
            }
            $data['cv_document'] = $request->file('cv_document')->store('cv_documents', 'public');
        }

        $applicant->update($data);

        // Bawa kembali query string (supaya filter/pagination tetap)
        return redirect()
            ->route('admin.applicant.index', $request->only('search','position','batch','page'))
            ->with('success','Data pelamar berhasil diperbarui.');
    }

    /**
     * DESTROY: konfirmasi via modal
     */
    public function destroy(Applicant $applicant)
    {
        if ($applicant->cv_document) {
            Storage::disk('public')->delete($applicant->cv_document);
        }
        $applicant->delete();

        return redirect()->route('admin.applicant.index')->with('success','Data pelamar berhasil dihapus.');
    }

    /**
     * EXPORT: mengikuti filter aktif (search + position + batch)
     * (Heading tebal diatur di ApplicantsExport via WithStyles)
     */
    public function export(Request $request)
    {
        $search     = trim((string) $request->query('search'));
        $positionId = $request->query('position');
        $batchId    = $request->query('batch');

        $fileName = 'data-pelamar-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(
            new ApplicantsExport($search, $positionId, $batchId),
            $fileName
        );
    }
}

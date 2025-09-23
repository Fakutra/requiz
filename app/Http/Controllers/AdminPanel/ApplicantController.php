<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\Position;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Exports\ApplicantsExport;
use Maatwebsite\Excel\Facades\Excel;

class ApplicantController extends Controller
{
    public function index(Request $request)
    {
        $applicants = $this->getFilteredApplicants($request)
            ->orderBy('id', 'asc')
            ->paginate(10);

        $positions = Position::orderBy('name')->get();

        return view('admin.applicant.index', compact('applicants', 'positions'));
    }

    public function export(Request $request)
    {
        $fileName = 'data-pelamar-' . now()->format('Y-m-d') . '.xlsx';
        $search   = $request->input('search'); // sesuaikan
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ApplicantsExport($search), $fileName);
    }

    private function getFilteredApplicants(Request $request)
    {
        $search     = $request->input('search');
        $status     = $request->input('status');
        $positionId = $request->input('position');

        $query = Applicant::query()->with('position')->orderBy('name', 'asc');

        $query->when($search, function ($q, $search) {
            return $q->where(function ($subQ) use ($search) {
                $subQ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(pendidikan) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(universitas) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(jurusan) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw("DATE_PART('year', AGE(CURRENT_DATE, tgl_lahir)) = ?", [(int) $search])
                    ->orWhereHas('position', function ($posQ) use ($search) {
                        $posQ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
                    });
            });
        });

        $query->when($status, fn ($q) => $q->where('status', $status));
        $query->when($positionId, fn ($q) => $q->where('position_id', $positionId));

        return $query;
    }

    // Dipakai oleh modal Edit di halaman seleksi & halaman index
    public function update(Request $request, Applicant $applicant)
    {
        $allowedPendidikan = ['SMA/Sederajat','Diploma','S1','S2','S3'];
        $allowedStatus = [
            'Seleksi Administrasi','Tes Tulis','Technical Test','Interview','Offering',
            'Tidak Lolos Seleksi Administrasi','Tidak Lolos Seleksi Tes Tulis',
            'Tidak Lolos Technical Test','Tidak Lolos interview',
            'Menerima Offering','Menolak Offering',
        ];

        $data = $request->validate([
            'name'        => ['required','string','max:255'],
            'email'       => ['required','email','max:255', Rule::unique('applicants','email')->ignore($applicant->id)],
            'nik'         => ['required','string','max:16'],
            'no_telp'     => ['required','string','max:14'],
            'tpt_lahir'   => ['required','string','max:255'],
            'tgl_lahir'   => ['required','date'],
            'alamat'      => ['required','string','max:255'],
            'pendidikan'  => ['required', Rule::in($allowedPendidikan)],
            'universitas' => ['required','string','max:255'],
            'jurusan'     => ['required','string','max:255'],
            'thn_lulus'   => ['nullable','digits:4'],
            'status'      => ['required', Rule::in($allowedStatus)],
            'cv_document' => ['nullable','file','mimes:pdf','max:3072'], // 3 MB
            'position_id' => ['required','exists:positions,id'],
        ]);

        if ($request->hasFile('cv_document')) {
            if ($applicant->cv_document) {
                Storage::disk('public')->delete($applicant->cv_document);
            }
            $data['cv_document'] = $request->file('cv_document')->store('cv_documents', 'public');
        }

        $applicant->update($data);
        return back()->with('success', 'Data pelamar berhasil diperbarui.');
    }

    public function destroy(Applicant $applicant)
    {
        $applicant->delete();
        return redirect()->route('admin.applicant.index')->with('success', 'Data pelamar berhasil dihapus.');
    }
}

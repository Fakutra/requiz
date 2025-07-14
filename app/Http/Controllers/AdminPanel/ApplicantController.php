<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Applicant;
use Illuminate\Support\Facades\Storage;

class ApplicantController extends Controller
{
    // public function index()
    // {
    //     $applicants = Applicant::with('position')->groupBy('status')->get();
    //     return view('admin.applicant.index', compact('applicants'));
    // }

    // $groupedApplicants = $applicants->groupBy('status');
    
    public function index(Request $request)
    {
        // =======================
        // FILTER: All Pelamar
        // =======================
        $searchAll = $request->input('search_all');
        $queryAll = Applicant::with('position');

        if (!empty($searchAll)) {
            $queryAll->where(function ($q) use ($searchAll) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($searchAll) . '%'])
                ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($searchAll) . '%'])
                ->orWhereRaw('LOWER(pendidikan) LIKE ?', ['%' . strtolower($searchAll) . '%'])
                ->orWhereRaw('LOWER(universitas) LIKE ?', ['%' . strtolower($searchAll) . '%'])
                ->orWhereRaw('LOWER(jurusan) LIKE ?', ['%' . strtolower($searchAll) . '%']);
            })->orWhereHas('position', function ($q) use ($searchAll) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($searchAll) . '%']);
            });
        }

        $applicantAll = $queryAll->orderBy('name')->paginate(10)->withQueryString();

        // =======================
        // FILTER: Screening
        // =======================
        $searchScreening = $request->input('search_screening');
        $screeningQuery = Applicant::with('position')
            ->where('status', 'Seleksi Administrasi');

        if (!empty($searchScreening)) {
            $screeningQuery->where(function ($q) use ($searchScreening) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($searchScreening) . '%'])
                ->orWhereRaw('LOWER(universitas) LIKE ?', ['%' . strtolower($searchScreening) . '%'])
                ->orWhereRaw('LOWER(jurusan) LIKE ?', ['%' . strtolower($searchScreening) . '%'])
                ->orWhereRaw('LOWER(pendidikan) LIKE ?', ['%' . strtolower($searchScreening) . '%']);
            });
        }

        $applicants = $screeningQuery->get()
            ->sortBy('name')
            ->groupBy(fn ($item) => $item->position->name ?? 'Tanpa Posisi');

        return view('admin.applicant.index', compact('applicants', 'applicantAll'));
    }




    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255',
            'nik'         => 'required|string|max:16',
            'no_telp'     => 'required|string|max:14',
            'tpt_lahir'   => 'required|string|max:255',
            'tgl_lahir'   => 'required|date',
            'alamat'      => 'required|string|max:255',
            'pendidikan'  => 'required|string|max:255',
            'universitas' => 'required|string|max:255',
            'jurusan'     => 'required|string|max:255',
            'cv_document' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $applicant = Applicant::findOrFail($id);

        // Update file jika ada file baru diupload
        if ($request->hasFile('cv_document')) {
            // Hapus file lama jika ada
            if ($applicant->cv_document && Storage::exists($applicant->cv_document)) {
                Storage::delete($applicant->cv_document);
            }
        
            // Simpan file baru
            $path = $request->file('cv_document')->store('cv-applicant', 'public');
            $validated['cv_document'] = $path;
        }

        $applicant->update($validated);

        return redirect()->route('applicant.index')->with('success', 'Data pelamar berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $applicants = Applicant::findOrFail($id);
        $applicants->delete();

        return redirect()->route('applicant.index')->with('success', 'Applicant has been deleted!');
    }

    public function search(Request $request)
    {
        $search = $request->get('search');

        $applicants = Applicant::where('name', 'like', "%$search%")
            ->orWhere('universitas', 'like', "%$search%")
            ->orWhere('jurusan', 'like', "%$search%")
            ->orWhere('pendidikan', 'like', "%$search%") // 👈 Tambahkan ini
            ->orderBy('name')
            ->get();

        return response()->json($applicants);
    }

    public function show($id)
    {
        $applicant = Applicant::with('position')->findOrFail($id);
        return view('admin.applicant.show', compact('applicant'));
    }

    public function updateStatus(Request $request)
{
    $request->validate([
        'selected_applicants' => 'required|array',
        'status' => 'required|string|in:Seleksi Tes Tulis,Tidak Lolos Seleksi Administrasi',
    ]);

    Applicant::whereIn('id', $request->selected_applicants)
             ->update(['status' => $request->status]);

    return back()->with('success', 'Status pelamar berhasil diperbarui.');
}


}

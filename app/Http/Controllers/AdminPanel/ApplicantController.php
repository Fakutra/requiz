<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Applicant;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Exports\ApplicantsExport; // <-- 1. Impor kelas Export Anda
use Maatwebsite\Excel\Facades\Excel; // <-- 2. Impor Fassad Excel
use App\Models\Position; // 1. Impor model Position

class ApplicantController extends Controller
{
    public function index(Request $request)
    {
        // 2. Panggil method terpusat dengan seluruh request
        $applicants = $this->getFilteredApplicants($request)->orderBy('id','asc')->paginate(10);

        // 3. Ambil data posisi untuk dikirim ke view (untuk mengisi dropdown filter)
        $positions = Position::orderBy('name')->get();

        // 4. Kirim data applicants dan positions ke view
        return view('admin.applicant.index', compact('applicants', 'positions'));
    }

    public function export(Request $request)
    {
        // 5. Pastikan export juga menggunakan semua filter
        $fileName = 'data-pelamar-' . date('Y-m-d') . '.xlsx';

        // Panggil kelas Export dengan seluruh request dan unduh filenya
        return Excel::download(new ApplicantsExport($request), $fileName);
    }

    /**
     * Method privat untuk mengambil data pelamar dengan filter
     */
    private function getFilteredApplicants(Request $request) // 6. Ubah parameter menjadi Request
    {
        // Ambil semua input dari request
        $search = $request->input('search');
        $status = $request->input('status');
        $positionId = $request->input('position');

        $query = Applicant::query()->with('position')->orderBy('name', 'asc');

        // Filter untuk pencarian umum (tetap seperti sebelumnya)
        $query->when($search, function ($q, $search) {
            return $q->where(function($subQ) use ($search) {
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

        // 7. TAMBAHKAN BLOK FILTER SPESIFIK (AND conditions)
        
        // Filter berdasarkan Status dari dropdown
        $query->when($status, function ($q, $status) {
            return $q->where('status', $status);
        });

        // Filter berdasarkan Posisi dari dropdown
        $query->when($positionId, function ($q, $positionId) {
            return $q->where('position_id', $positionId);
        });

        return $query;
    }

    /**
     * BARU: Method untuk memperbarui data pelamar.
     */
    public function update(Request $request, Applicant $applicant)
    {
        // Validasi data (termasuk alamat dan thn_lulus yang sudah diperbaiki di form)
        $validatedData = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255',
            'nik'         => 'required|string|max:16',
            'no_telp'     => 'required|string|max:14',
            'tpt_lahir'   => 'required|string|max:255',
            'tgl_lahir'   => 'required|date',
            'alamat'      => 'required|string|max:255', // Pastikan field ini ada di form
            'pendidikan'  => 'required|string|max:255',
            'universitas' => 'required|string|max:255',
            'jurusan'     => 'required|string|max:255',
            'thn_lulus'   => 'required|string|max:4',  // Pastikan name="thn_lulus" di form
            'status'      => 'required|string',
            'cv_document' => 'nullable|file|mimes:pdf|max:3072', // max 3MB
            'position_id' => 'required|exists:positions,id',
            // 'skills' tidak perlu divalidasi jika readonly, kecuali ingin bisa diubah
        ]);

        // Cek jika ada file CV baru yang di-upload
        if ($request->hasFile('cv_document')) {
            // Hapus file lama jika ada
            if ($applicant->cv_document) {
                Storage::delete('public/' . $applicant->cv_document);
            }
            // Simpan file baru dan dapatkan path-nya
            $path = $request->file('cv_document')->store('cv_documents', 'public');
            $validatedData['cv_document'] = $path;
        }

        // Update data pelamar
        $applicant->update($validatedData);

        // Redirect kembali ke halaman index dengan pesan sukses
        // Pastikan route 'applicant.index' ada, jika tidak sesuaikan (misal: 'admin.applicant.index')
        return redirect()->route('applicant.index')->with('success', 'Data pelamar berhasil diperbarui.');
    }

    /**
     * BARU: Method untuk menghapus data pelamar.
     */
    public function destroy(Applicant $applicant)
    {
        $applicant->delete();
        return redirect()->route('applicant.index')->with('success', 'Data pelamar berhasil dihapus.');
    }
    public function destroySeleksi(Applicant $applicant)
    {
        $applicant->delete();
        return redirect()->route('applicant.seleksi.index')->with('success', 'Data pelamar berhasil dihapus.');
    }

    public function seleksiIndex()
    {
        $rekap = [
            'Administrasi' => [
                'lolos' => Applicant::where('status', 'Seleksi Tes Tulis')->count(),
                'gagal' => Applicant::where('status', 'Tidak Lolos Seleksi Administrasi')->count(),
                'route' => 'Seleksi Administrasi',
            ],
            'Tes Tulis' => [
                'lolos' => Applicant::where('status', 'Seleksi Tes Praktek')->count(),
                'gagal' => Applicant::where('status', 'Tidak Lolos Seleksi Tes Tulis')->count(),
                'route' => 'Seleksi Tes Tulis',
            ],
            'Tes Praktek' => [
                'lolos' => Applicant::where('status', 'Interview')->count(),
                'gagal' => Applicant::where('status', 'Tidak Lolos Seleksi Tes Praktek')->count(),
                'route' => 'Seleksi Tes Praktek',
            ],
            'Interview' => [
                'lolos' => Applicant::where('status', 'Lolos Interview')->count(),
                'gagal' => Applicant::where('status', 'Tidak Lolos Interview')->count(),
                'route' => 'Interview',
            ],
        ];

        return view('admin.applicant.seleksi.index', compact('rekap'));
    }

    public function editSeleksi($id)
    {
        $applicant = Applicant::with('position')->findOrFail($id);

        return view('admin.applicants.edit-seleksi', compact('applicant'));
    }


public function process(Request $request, string $stage)
{
    $positions  = Position::all();
    $allJurusan = Applicant::whereNotNull('jurusan')->distinct()->pluck('jurusan');

    $q = Applicant::with('position');

    // Default: tampilkan yang sedang di tahap ini
    if ($request->filled('status')) {
        $q->where('status', $request->status);
    } else {
        $q->where('status', $stage);
    }

    if ($s = $request->search) {
        $q->where(function($x) use ($s) {
            $x->where('name','like',"%$s%")
              ->orWhere('email','like',"%$s%")
              ->orWhere('jurusan','like',"%$s%");
        });
    }

    if ($request->filled('position')) $q->where('position_id', $request->position);
    if ($request->filled('jurusan'))  $q->where('jurusan', $request->jurusan);

    // $applicants = $q->withQueryString()->paginate(20);
    $applicants = $q->paginate(20)->appends(request()->query());

    return view('admin.applicant.seleksi.process', compact('applicants','positions','allJurusan','stage'));
}



/**
 * (Opsional) helper untuk menentukan stage berikutnya saat filter "lolos"
 */
private function nextStage(string $stage): string
{
    $map = [
        'Seleksi Administrasi'  => 'Seleksi Tes Tulis',
        'Seleksi Tes Tulis'     => 'Seleksi Technical Test',
        'Seleksi Technical Test'=> 'Seleksi Interview',
        'Seleksi Interview'     => 'Interview',
        'Interview'             => 'Interview', // terakhir
    ];
    return $map[$stage] ?? $stage;
}

    public function sendEmail(Request $request)
    {
        $request->validate([
            'recipients' => 'required|string',             // akan diisi dari hidden input (join koma)
            'subject'    => 'required|string|max:255',
            'message'    => 'required|string|max:20000',
        ]);

        // Normalisasi pemisah: baris baru & titik koma -> koma
        $raw = str_replace(["\r\n", "\n", ";"], ",", $request->recipients);

        // Pecah jadi array email unik & rapi
        $emails = collect(explode(',', $raw))
            ->map(fn ($e) => trim($e))
            ->filter()         // buang kosong
            ->unique()
            ->values();

        if ($emails->isEmpty()) {
            return back()
                ->withErrors(['recipients' => 'Belum ada penerima yang dipilih.'])
                ->withInput();
        }

        // Validasi format email satu per satu
        $invalid = $emails->reject(fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL));
        if ($invalid->isNotEmpty()) {
            return back()
                ->withErrors(['recipients' => 'Email tidak valid: '.$invalid->implode(', ')])
                ->withInput();
        }

        // Kirim satu per satu (sederhana; bisa diganti queue bila banyak)
        $failed = [];
        foreach ($emails as $to) {
            try {
                Mail::raw($request->message, function ($mail) use ($to, $request) {
                    $mail->to($to)
                        ->subject($request->subject)
                        ->from(config('mail.from.address'), config('mail.from.name'));
                });
            } catch (\Throwable $e) {
                $failed[] = $to;
                Log::error('Gagal kirim email', ['to' => $to, 'error' => $e->getMessage()]);
            }
        }

        $total  = $emails->count();
        $sent   = $total - count($failed);

        if ($sent === 0) {
            return back()->with('error', 'Semua pengiriman gagal. Cek konfigurasi MAIL_* di .env dan log aplikasi.');
        }

        $msg = "Email terkirim ke {$sent} dari {$total} penerima.";
        if (!empty($failed)) {
            $msg .= ' Gagal: '.implode(', ', $failed);
        }

        return back()->with('success', $msg);
    }


    public function updateStatus(Request $request)
    {
        $request->validate([
            'stage' => 'required|string',
            'selected_applicants' => 'required|array',
            'status' => 'required|array',
        ]);

        foreach ($request->selected_applicants as $applicantId) {
            $applicant = Applicant::findOrFail($applicantId);
            $selectedStatus = $request->status[$applicantId] ?? null;

            if ($selectedStatus === 'lolos') {
                if ($request->stage === 'Seleksi Administrasi') {
                    $applicant->status = 'Seleksi Tes Tulis';
                } elseif ($request->stage === 'Seleksi Tes Tulis') {
                    $applicant->status = 'Seleksi Tes Praktek';
                } elseif ($request->stage === 'Seleksi Tes Praktek') {
                    $applicant->status = 'Interview';
                } elseif ($request->stage === 'Interview') {
                    $applicant->status = 'Lolos Interview';
                }
            } elseif ($selectedStatus === 'tidak_lolos') {
                $applicant->status = 'Tidak Lolos ' . $request->stage;
            }

            $applicant->save();
        }

        return back()->with('success', 'Status peserta berhasil diperbarui.');
    }


    public function showStageApplicants($stage)
    {
        $query = Applicant::with('user', 'position')
            ->where('status', 'LIKE', "%$stage%");

        if (request('jurusan')) {
            $query->where('jurusan', request('jurusan'));
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        $applicants = $query->get();

        // ambil semua jurusan unik
        $allJurusan = Applicant::select('jurusan')->distinct()->pluck('jurusan');

        // ambil status yang hanya relevan dengan tahap ini
        $filteredStatuses = [
            "Lolos $stage",
            "Tidak Lolos $stage"
        ];

        return view('admin.applicant.seleksi.process', compact('applicants', 'stage', 'allJurusan', 'filteredStatuses'));
    }

}


    // public function index(Request $request)
    // {
    //     // Ambil kata kunci pencarian dari request
    //     $search = $request->input('search');

    //     // Query dasar dengan relasi dan urutan
    //     $query = Applicant::with('position')->orderBy('name', 'asc');

    //     // Terapkan filter pencarian HANYA JIKA ada input 'search'
    //     $query->when($search, function ($q, $search) {
    //         return $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%'])
    //                   ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($search) . '%'])
    //                   ->orWhereRaw('LOWER(pendidikan) LIKE ?', ['%' . strtolower($search) . '%'])
    //                   ->orWhereRaw('LOWER(universitas) LIKE ?', ['%' . strtolower($search) . '%'])
    //                   ->orWhereRaw('LOWER(jurusan) LIKE ?', ['%' . strtolower($search) . '%'])
    //                   ->orWhereRaw("DATE_PART('year', AGE(CURRENT_DATE, tgl_lahir)) = ?", [(int) $search])
    //                   ->orWhereHas('position', function ($subQ) use ($search) {
    //                      $subQ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
    //                  });
    //     });

    //     // Lakukan paginasi pada hasil query
    //     $applicants = $query->paginate(15);

    //     // Kirim data ke view
    //     return view('admin.applicant.index', compact('applicants'));
    // }

    // $groupedApplicants = $applicants->groupBy('status');
    
    // public function index(Request $request)
    // {
    //     // =======================
    //     // FILTER: All Pelamar
    //     // =======================
    //     $searchAll = $request->input('search_all');
    //     $queryAll = Applicant::with('position');

    //     if (!empty($searchAll)) {
    //         $queryAll->where(function ($q) use ($searchAll) {
    //             $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($searchAll) . '%'])
    //             ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($searchAll) . '%'])
    //             ->orWhereRaw('LOWER(pendidikan) LIKE ?', ['%' . strtolower($searchAll) . '%'])
    //             ->orWhereRaw('LOWER(universitas) LIKE ?', ['%' . strtolower($searchAll) . '%'])
    //             ->orWhereRaw('LOWER(jurusan) LIKE ?', ['%' . strtolower($searchAll) . '%']);
    //         })->orWhereHas('position', function ($q) use ($searchAll) {
    //             $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($searchAll) . '%']);
    //         });
    //     }

    //     $applicantAll = $queryAll->orderBy('name')->paginate(10)->withQueryString();

    //     // =======================
    //     // FILTER: Screening
    //     // =======================
    //     $searchScreening = $request->input('search_screening');
    //     $screeningQuery = Applicant::with('position')
    //         ->where('status', 'Seleksi Administrasi');

    //     if (!empty($searchScreening)) {
    //         $screeningQuery->where(function ($q) use ($searchScreening) {
    //             $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($searchScreening) . '%'])
    //             ->orWhereRaw('LOWER(universitas) LIKE ?', ['%' . strtolower($searchScreening) . '%'])
    //             ->orWhereRaw('LOWER(jurusan) LIKE ?', ['%' . strtolower($searchScreening) . '%'])
    //             ->orWhereRaw('LOWER(pendidikan) LIKE ?', ['%' . strtolower($searchScreening) . '%']);
    //         });
    //     }

    //     $applicants = $screeningQuery->get()
    //         ->sortBy('name')
    //         ->groupBy(fn ($item) => $item->position->name ?? 'Tanpa Posisi');

    //     return view('admin.applicant.index', compact('applicants', 'applicantAll'));
    // }




    // public function update(Request $request, $id)
    // {
    //     $validated = $request->validate([
    //         'name'        => 'required|string|max:255',
    //         'email'       => 'required|email|max:255',
    //         'nik'         => 'required|string|max:16',
    //         'no_telp'     => 'required|string|max:14',
    //         'tpt_lahir'   => 'required|string|max:255',
    //         'tgl_lahir'   => 'required|date',
    //         'alamat'      => 'required|string|max:255',
    //         'pendidikan'  => 'required|string|max:255',
    //         'universitas' => 'required|string|max:255',
    //         'jurusan'     => 'required|string|max:255',
    //         'cv_document' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
    //     ]);

    //     $applicant = Applicant::findOrFail($id);

    //     // Update file jika ada file baru diupload
    //     if ($request->hasFile('cv_document')) {
    //         // Hapus file lama jika ada
    //         if ($applicant->cv_document && Storage::exists($applicant->cv_document)) {
    //             Storage::delete($applicant->cv_document);
    //         }
        
    //         // Simpan file baru
    //         $path = $request->file('cv_document')->store('cv-applicant', 'public');
    //         $validated['cv_document'] = $path;
    //     }

    //     $applicant->update($validated);

    //     return redirect()->route('applicant.index')->with('success', 'Data pelamar berhasil diperbarui.');
    // }

    // public function destroy($id)
    // {
    //     $applicants = Applicant::findOrFail($id);
    //     $applicants->delete();

    //     return redirect()->route('applicant.index')->with('success', 'Applicant has been deleted!');
    // }

    // public function search(Request $request)
    // {
    //     $search = $request->get('search');

    //     $applicants = Applicant::where('name', 'like', "%$search%")
    //         ->orWhere('universitas', 'like', "%$search%")
    //         ->orWhere('jurusan', 'like', "%$search%")
    //         ->orWhere('pendidikan', 'like', "%$search%") // 👈 Tambahkan ini
    //         ->orderBy('name')
    //         ->get();

    //     return response()->json($applicants);
    // }

    // public function show($id)
    // {
    //     $applicant = Applicant::with('position')->findOrFail($id);
    //     return view('admin.applicant.show', compact('applicant'));
    // }

    // public function updateStatus(Request $request)
    // {
    //     $request->validate([
    //         'selected_applicants' => 'required|array',
    //         'status' => 'required|string|in:Seleksi Tes Tulis,Tidak Lolos Seleksi Administrasi',
    //     ]);

    //     Applicant::whereIn('id', $request->selected_applicants)
    //             ->update(['status' => $request->status]);

    //     return back()->with('success', 'Status pelamar berhasil diperbarui.');
    // }
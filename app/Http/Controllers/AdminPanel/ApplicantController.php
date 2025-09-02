<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Applicant;
use Illuminate\Support\Facades\Storage;
use App\Models\SelectionLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
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
        $stages = [
            'Seleksi Administrasi',
            'Seleksi Tes Tulis',
            'Seleksi Tes Praktek',
            'Interview',
        ];

        $rekap = [];

        foreach ($stages as $stage) {
            $stageKey = Str::slug($stage);

            // Ambil id log TERBARU per applicant untuk stage ini
            $latestIds = SelectionLog::where('stage_key', $stageKey)
                ->select(DB::raw('MAX(id) AS id'))
                ->groupBy('applicant_id')
                ->pluck('id');

            if ($latestIds->isEmpty()) {
                // Belum ada log: tampilkan 0 tapi tetap render baris
                $rekap[$stage] = ['lolos' => 0, 'gagal' => 0, 'route' => $stage];
                continue;
            }

            $counts = SelectionLog::whereIn('id', $latestIds)
                ->selectRaw("
                    SUM(CASE WHEN result='lolos' THEN 1 ELSE 0 END) AS lolos,
                    SUM(CASE WHEN result='tidak_lolos' THEN 1 ELSE 0 END) AS gagal
                ")
                ->first();

            $rekap[$stage] = [
                'lolos' => (int) ($counts->lolos ?? 0),
                'gagal' => (int) ($counts->gagal ?? 0),
                'route' => $stage,
            ];
        }

        return view('admin.applicant.seleksi.index', compact('rekap'));
    }

    public function editSeleksi($id)
    {
        $applicant = Applicant::with('position')->findOrFail($id);

        return view('admin.applicants.edit-seleksi', compact('applicant'));
    }


    // App\Http\Controllers\AdminPanel\ApplicantController.php

    public function process(Request $request, string $stage)
    {
        $positions  = \App\Models\Position::orderBy('name')->get();
        $allJurusan = \App\Models\Applicant::whereNotNull('jurusan')->distinct()->pluck('jurusan');

        $nextStage = $this->nextStage($stage);

        $q = \App\Models\Applicant::with('position');

        if ($request->filled('status')) {
            $q->where('status', $request->status);
        } else {
            // tampilkan: sedang tahap ini, lolos tahap ini (2 pola), dan tidak lolos tahap ini
            $q->where(function ($w) use ($stage, $nextStage) {
                $w->where('status', $stage)                 // sedang tahap ini
                ->orWhere('status', 'Lolos '.$stage)      // eksplisit
                ->orWhere('status', $nextStage)           // sudah pindah (means lolos tahap ini)
                ->orWhere('status', 'Tidak Lolos '.$stage);
            });
        }

        if ($s = $request->search) {
            $q->where(function($x) use ($s) {
                $x->where('name','like',"%{$s}%")
                ->orWhere('email','like',"%{$s}%")
                ->orWhere('jurusan','like',"%{$s}%");
            });
        }
        if ($request->filled('position')) $q->where('position_id', $request->position);
        if ($request->filled('jurusan'))  $q->where('jurusan', $request->jurusan);

        $applicants = $q->orderBy('name')->paginate(20)->appends($request->query());

        // Tandai state untuk blade (badge + auto select lolos)
        $applicants->getCollection()->transform(function ($a) use ($stage, $nextStage) {
            if ($a->status === $stage) {
                $a->_stage_state  = 'current';
                $a->_stage_status = $stage;
                $a->_stage_badge  = 'bg-gray-100 text-gray-800 border border-gray-200';
            } elseif ($a->status === 'Tidak Lolos '.$stage) {
                $a->_stage_state  = 'gagal';
                $a->_stage_status = 'Tidak Lolos '.$stage;
                $a->_stage_badge  = 'bg-red-50 text-red-700 border border-red-200';
            } elseif ($a->status === 'Lolos '.$stage || $a->status === $nextStage) {
                // dianggap Lolos tahap ini
                $a->_stage_state  = 'lolos';
                $a->_stage_status = 'Lolos '.$stage;
                $a->_stage_badge  = 'bg-green-50 text-green-700 border border-green-200';
            } else {
                $a->_stage_state  = 'other';
                $a->_stage_status = $a->status;
                $a->_stage_badge  = 'bg-slate-50 text-slate-600 border border-slate-200';
            }
            return $a;
        });

        return view('admin.applicant.seleksi.process', compact('applicants','positions','allJurusan','stage'));
    }

    // Pastikan map next stage benar
    private function nextStage(string $stage): string
    {
        $map = [
            'Seleksi Administrasi' => 'Seleksi Tes Tulis',
            'Seleksi Tes Tulis'    => 'Seleksi Tes Praktek',
            'Seleksi Tes Praktek'  => 'Interview',
            'Interview'            => 'Lolos Interview', // terminal
        ];
        return $map[$stage] ?? $stage;
    }

    public function sendEmail(Request $request)
    {
        $request->validate([
            'recipients'    => 'required|string',           // join koma (dibuat di modal)
            'recipient_ids' => 'required|string',           // join koma (untuk ambil nama)
            'stage'         => 'required|string',
            'use_template'  => 'nullable|boolean',
            'subject'       => 'nullable|string|max:255',
            'message'       => 'nullable|string|max:20000',
            'attachment'    => 'required|file|mimes:pdf|max:5120', // max 5MB
        ]);

        $useTemplate = $request->boolean('use_template', true);

        $rawEmails = str_replace(["\r\n", "\n", ";"], ",", $request->recipients);
        $emails = collect(explode(',', $rawEmails))
            ->map(fn($e) => trim($e))
            ->filter()
            ->unique()
            ->values();

        $ids = collect(explode(',', $request->recipient_ids))
            ->map(fn($v) => trim($v))
            ->filter()
            ->map(fn($v) => (int) $v)
            ->values();

        $applicants = \App\Models\Applicant::whereIn('id', $ids)->get();

        if ($emails->isEmpty() || $applicants->isEmpty()) {
            return back()->withErrors(['recipients' => 'Penerima tidak valid/ kosong.'])->withInput();
        }

        $file   = $request->file('attachment');
        $failed = [];
        $total  = $emails->count();

        foreach ($emails as $to) {
            $fullName = optional($applicants->firstWhere('email', $to))->name ?? $to;

            if ($useTemplate) {
                ['subject' => $subject, 'message' => $body] = $this->defaultEmailForStage($request->stage, $fullName);
            } else {
                $subject = $request->input('subject', 'Informasi Seleksi');
                $body    = $request->input('message', '');
            }

            try {
                Mail::raw($body, function ($mail) use ($to, $subject, $file) {
                    $mail->to($to)
                        ->subject($subject)
                        ->from(config('mail.from.address'), config('mail.from.name'))
                        ->attach($file->getRealPath(), [
                            'as'   => $file->getClientOriginalName(),
                            'mime' => 'application/pdf',
                        ]);
                });
            } catch (\Throwable $e) {
                $failed[] = $to;
                Log::error('Gagal kirim email', ['to' => $to, 'error' => $e->getMessage()]);
            }
        }

        $sent = $total - count($failed);
        if ($sent === 0) {
            return back()->with('error', 'Semua pengiriman gagal. Cek konfigurasi email & log aplikasi.');
        }

        $msg = "Email terkirim ke {$sent} dari {$total} penerima.";
        if (!empty($failed)) $msg .= ' Gagal: ' . implode(', ', $failed);

        return back()->with('success', $msg);
    }

    private function defaultEmailForStage(string $stage, string $fullName): array
    {
        $subject = "Pemberitahuan Hasil {$stage}";
        $body = "Kepada {$fullName}

    Selamat anda lolos pada tahap {$stage}. Silahkan cek jadwal anda di file yang telah dikirimkan.

    Demikian
    Admin ReQuiz";
        return ['subject' => $subject, 'message' => $body];
    }


    public function updateStatus(Request $request)
    {
        $request->validate([
            'stage'               => 'required|string',
            'selected_applicants' => 'required|array',
            'status'              => 'required|array', // [id => 'lolos'|'tidak_lolos']
        ]);

        $stage    = (string) $request->stage;
        $stageKey = \Illuminate\Support\Str::slug($stage);

        DB::transaction(function () use ($request, $stage, $stageKey) {
            foreach ($request->selected_applicants as $applicantId) {
                $applicant = \App\Models\Applicant::with('position')->findOrFail($applicantId);
                $action    = $request->status[$applicantId] ?? null;

                if ($action === 'lolos') {
                    $next = $this->nextStage($stage);
                    $applicant->status = $next;
                    $applicant->save();

                    \App\Models\SelectionLog::create([
                        'applicant_id' => $applicant->id,
                        'stage'        => $stage,
                        'stage_key'    => $stageKey,
                        'result'       => 'lolos',
                        'position_id'  => $applicant->position_id,
                        'jurusan'      => $applicant->jurusan,
                        'acted_by'     => auth()->id(),
                    ]);

                } elseif ($action === 'tidak_lolos') {
                    $applicant->status = 'Tidak Lolos '.$stage;
                    $applicant->save();

                    \App\Models\SelectionLog::create([
                        'applicant_id' => $applicant->id,
                        'stage'        => $stage,
                        'stage_key'    => $stageKey,
                        'result'       => 'tidak_lolos',
                        'position_id'  => $applicant->position_id,
                        'jurusan'      => $applicant->jurusan,
                        'acted_by'     => auth()->id(),
                    ]);
                }
            }
        });

        return back()->with('success', 'Status peserta & log berhasil diperbarui.');
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
<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Batch;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class LowonganController extends Controller
{
    public function index()
    {
        // Mengambil posisi yang statusnya 'Active' DAN batch terkaitnya juga 'Active'
        $positions = Position::withCount('applicants')
            ->where('status', 'Active') // 1. Filter status di tabel 'positions'
            ->whereHas('batch', function ($query) { // 2. Tambahkan filter berdasarkan relasi 'batch'
                $query->where('status', 'Active'); // 3. Pastikan status di tabel 'batches' adalah 'Active'
            })
            ->orderBy('id', 'asc')
            ->get();

        // Bagian ini tetap sama untuk mengecek riwayat lamaran user
        $appliedBatchIds = Applicant::where('user_id', auth()->id())
            ->pluck('batch_id')
            ->toArray();

        return view('lowongan', compact('positions', 'appliedBatchIds'));
    }

    public function store(Request $request, $positionSlug)
    {
        // Ambil posisi berdasarkan slug
        $position = Position::where('slug', $positionSlug)->firstOrFail();

        // Ambil ID user & batch
        $userId = auth()->id();
        $batchId = $position->batch_id;

        // Cek apakah user sudah pernah melamar di batch ini
        $alreadyApplied = Applicant::where('user_id', $userId)
            ->where('batch_id', $batchId)
            ->exists();

        if ($alreadyApplied) {
            return redirect()->route('lowongan.index')
                ->withErrors(['error' => 'Anda sudah melamar di batch ini. Anda hanya boleh melamar 1 posisi per batch.']);
        }

        // Cek apakah kuota penuh
        if ($position->applicants_count >= $position->quota) {
            return redirect()->route('lowongan.index')
                ->withErrors(['error' => 'Maaf, kuota untuk posisi ini sudah penuh.']);
        }

        // Validasi input form
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'nik'           => 'required|digits:16',
            'no_telp'       => 'required|string|max:14',
            'tpt_lahir'     => 'required|string|max:255',
            'tgl_lahir'     => 'required|date',
            'alamat'        => 'required|string',
            'pendidikan'    => 'required',
            'universitas'   => 'required|string',
            'jurusan'       => 'required|string',
            'thn_lulus'     => 'required|string|max:4',
            'skills'        => 'array',
            'cv_document'   => 'required|file|mimes:pdf|max:3072',
        ]);

        // Handle file upload
        $validated['cv_document'] = $request->file('cv_document')->store('cv-applicant', 'public');

        // Tambahkan kolom tambahan
        $validated['user_id']     = $userId;
        $validated['position_id'] = $position->id;
        $validated['batch_id']    = $batchId;

        // Tangani skill
        $skills = $request->input('skills', []);

        // Jika "Lainnya" dicentang dan ada input teksnya
        if (in_array('Lainnya', $skills) && $request->filled('other_skill')) {
            $key = array_search('Lainnya', $skills);
            $skills[$key] = $request->input('other_skill');
        }

        $validated['skills'] = implode(', ', $skills);

        // Simpan ke database
        Applicant::create($validated);

        return redirect()->route('lowongan.index')
            ->with('success', 'Selamat, lamaran anda telah berhasil dikirim!');
    }

    // public function index()
    // {
    //     $userId = auth()->id();

    //     $positions = Position::withCount('applicants')
    //         ->with(['applicants' => function ($query) use ($userId) {
    //             // Hanya muat relasi applicant jika user_id-nya adalah user yang sedang login
    //             $query->where('user_id', $userId);
    //         }])
    //         ->where('status', 'Active')
    //         ->orderBy('id', 'asc')
    //         ->get();

    //     return view('lowongan', compact('positions'));
    // }

    // public function store(Request $request, Position $position)
    // {
    //     // 1. Validasi (tetap sama, sudah bagus)
    //     $validated = $request->validate([
    //         'name'        => 'required|string|max:255',
    //         'email'       => 'required|string|max:255',
    //         'nik'         => 'required|digits:16',
    //         'no_telp'     => 'required|string|max:14',
    //         'tpt_lahir'   => 'required|string|max:255',
    //         'tgl_lahir'   => 'required|date',
    //         'alamat'      => 'required',
    //         'pendidikan'  => 'required',
    //         'universitas' => 'required|string',
    //         'jurusan'     => 'required|string',
    //         'thn_lulus'   => 'required|digits:4',
    //         'skill'       => 'nullable|string', // Menggunakan nullable agar tidak wajib diisi
    //         'cv_document' => 'required|file|mimes:pdf|max:3072',
    //     ]);

    //     // 2. Handle file upload
    //     $cvPath = $request->file('cv_document')->store('cv-applicant', 'public');

    //     // 3. Siapkan data untuk disimpan
    //     $dataToCreate = array_merge($validated, [
    //         'user_id'     => auth()->id(),
    //         'position_id' => $position->id,
    //         'batch_id'    => $position->batch_id, // <-- INI YANG DITAMBAHKAN
    //         'cv_document' => $cvPath,
    //     ]);

    //     // Tangani skill
    //     $skills = $request->input('skills', []);

    //     // Jika "Lainnya" dicentang dan ada input teksnya
    //     if (in_array('Lainnya', $skills) && $request->filled('other_skill')) {
    //         $key = array_search('Lainnya', $skills);
    //         $skills[$key] = $request->input('other_skill');
    //     }

    //     $validated['skills'] = implode(', ', $skills);

    //     // 4. Buat data pelamar
    //     Applicant::create($dataToCreate);

    //     // 5. Redirect dengan pesan sukses
    //     return redirect()->route('lowongan.index')
    //                     ->with('success', 'Selamat, lamaran Anda telah berhasil dikirim!');
    // }

}


// ② LOG: masuk ke method + request mentah
// Log::info('LowonganController@store dipanggil', [
//     'user_id'     => auth()->id(),
//     'position_id' => $position->id,
//     'request'     => $request->all(),      // JANGAN di production kalau ada file besar
// ]);

// try {
//     // ③ VALIDASI
//     $validated = $request->validate([
//         'name'          => 'required|string|max:255',
//         'email'         => 'required|string|max:255',
//         'nik'           => 'required|string|max:16',
//         'no_telp'       => 'required|string|max:14',
//         'tpt_lahir'     => 'required|string|max:255',
//         'tgl_lahir'     => 'required|date',
//         'alamat'        => 'required',
//         'pendidikan'    => 'required',
//         'universitas'   => 'required|string',
//         'cv'            => 'file|mimes:pdf|max:1024',
//         'doc_tambahan'  => 'file|mimes:pdf|max:1024',
//     ]);

//     // ④ HANDLE FILE
//     if ($request->file('cv')) {
//         $validated['cv'] = $request->file('cv')
//                                    ->store('cv-applicant');
//     }
//     if ($request->file('doc_tambahan')) {
//         $validated['doc_tambahan'] = $request->file('doc_tambahan')
//                                             ->store('doc-applicant');
//     }

//     // ⑤ TAMBAH FK
//     $validated['user_id']     = auth()->user()->id;
//     $validated['position_id'] = $position->id;

//     // ⑥ LOG: data siap insert
//     Log::debug('Data divalidasi & siap insert', $validated);

//     Applicant::create($validated);

//     // ⑦ LOG: sukses
//     Log::info('Lamaran tersimpan', [
//         'applicant_name' => $validated['name'],
//         'applicant_id'   => auth()->id(),
//         'position_id'    => $position->id,
//     ]);

//     return redirect()->route('lowongan')
//                      ->with('success', 'Selamat, lamaran anda telah berhasil dikirim');
// } catch (Throwable $e) {
//     // ⑧ LOG: error detail + trace
//     Log::error('Gagal menyimpan lamaran', [
//         'error' => $e->getMessage(),
//         'trace' => $e->getTraceAsString(),
//     ]);

//     // ⑨ dd() untuk debug lokal — hapus di production
//     dd($e->getMessage());

//     // Jika tidak dd(), kembalikan response elegan
//     // return back()->withErrors('Terjadi kesalahan: ' . $e->getMessage())
//     //              ->withInput();
// }

// public function create($slug)
    // {
    //     // $user = User::class;
    //     // $lowongans = Position::class;
    //     // return view('apply', compact('lowongans'));

    //     $positions = Position::where('slug', $slug)->first();
    //     return view('apply', compact('positions'));

    // }
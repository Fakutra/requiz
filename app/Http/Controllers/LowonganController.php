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
        $positions = Position::withCount('applicants')
                            ->where('status', 'Active')
                            ->orderBy('id', 'asc')
                            ->get();

        return view('lowongan', compact('positions'));
    }

    // public function index()
    // {
    //     // $lowongans = Position::where('status', 'active')->orderBy('id', 'asc')->get();
    //     // $batchs = Batch::with('position')->where('status', 'Active')->orderBy('id', 'asc')->get();
    //     // $positions = Position::withCount('applicant')->get();
    //     $batchs = Batch::with(['position' => fn ($q) => $q->withCount('applicant')])->get();

    //     return view('lowongan', compact('batchs'));
    // }

    public function create($batchSlug, $positionSlug)
    {
        $batch = Batch::where('slug', $batchSlug)->firstOrFail();
        $position = Position::where('slug', $positionSlug)
            ->where('batch_id', $batch->id)
            ->firstOrFail();

        return view('apply', compact('batch', 'position'));
    }

    public function store(Request $request, $positionSlug)
    {
        
        $position = Position::where('slug', $positionSlug)
                            ->firstOrFail();
        if ($position->applicants_count >= $position->quota) {
            return redirect()->route('lowongan.index')->withErrors(['error' => 'Maaf, kuota untuk posisi ini sudah penuh.']);
        }
        // validasi
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|max:255',
            'nik'           => 'required|digits:16',
            'no_telp'       => 'required|string|max:14',
            'tpt_lahir'     => 'required|string|max:255',
            'tgl_lahir'     => 'required|date',
            'alamat'        => 'required',
            'pendidikan'    => 'required',
            'universitas'   => 'required|string',
            'jurusan'   => 'required|string',
            'cv_document'            => 'required|file|mimes:pdf|max:3072',
            // 'doc_tambahan'  => 'required|file|mimes:pdf|max:1024',
        ]);

        // Tambahkan posisi ID ke old input agar bisa dibuka kembali
        $request->merge(['position_id' => $position->id]);

        // handle file
        $validated['cv_document'] = $request->file('cv_document')->store('cv-applicant', 'public');

        $validated['user_id']     = auth()->user()->id;
        $validated['position_id'] = $position->id;

        Applicant::create($validated);

        return redirect()->route('lowongan.index')
                        ->with('failed', 'Maaf, lamaran gagal dikirim. Periksa kembali data diri anda!')
                        ->with('success', 'Selamat, lamaran anda telah berhasil dikirim!');
    }

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
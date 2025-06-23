<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use Illuminate\Http\Request;
use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Throwable;

class LowonganController extends Controller
{
    public function index()
    {
        $lowongans = Position::where('status', 'active')->orderBy('id', 'asc')->get();
        return view('lowongan', compact('lowongans'));
    }

    public function create($slug)
    {
        // $user = User::class;
        // $lowongans = Position::class;
        // return view('apply', compact('lowongans'));

        $positions = Position::where('slug', $slug)->first();
        return view('apply', compact('positions'));

    }

    public function store(Request $request, Position $position)   // pakai binding lebih aman
    {
        $validated = $request->validate([
                    
                    'name'          => 'required|string|max:255',
                    'email'         => 'required|string|max:255',
                    'nik'           => 'required|string|max:16',
                    'no_telp'       => 'required|string|max:14',
                    'tpt_lahir'     => 'required|string|max:255',
                    'tgl_lahir'     => 'required|date',
                    'alamat'        => 'required',
                    'pendidikan'    => 'required',
                    'universitas'   => 'required|string',
                    'cv'            => 'file|mimes:pdf|max:1024',
                    'doc_tambahan'  => 'file|mimes:pdf|max:1024',
                ]);
        // ④ HANDLE FILE
        if ($request->file('cv')) {
            $validated['cv'] = $request->file('cv')
                                    ->store('cv-applicant');
        }
        if ($request->file('doc_tambahan')) {
            $validated['doc_tambahan'] = $request->file('doc_tambahan')
                                                ->store('doc-applicant');
        }

        $validated['user_id']     = auth()->user()->id;
        $validated['position_id'] = $position->id;

        Applicant::create($validated);

        return redirect()->route('lowongan.index')
                     ->with('success', 'Selamat, lamaran anda telah berhasil dikirim');
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
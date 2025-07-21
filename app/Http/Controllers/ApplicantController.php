<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ApplicantController extends Controller
{
    public function create()
    {
        $applicant = Auth::user()->applicant;
        return view('applicant.form', compact('applicant'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|max:20',
            'no_telp' => 'required|string|max:20',
            'tpt_lahir' => 'required|string|max:255',
            'tgl_lahir' => 'required|date',
            'alamat_ktp' => 'required|string',
            'pendidikan' => 'required|in:SD,SMP,SMA,D3,S1,S2,S3',
            'cv' => $request->applicant ? 'nullable|file|mimes:pdf,doc,docx' : 'required|file|mimes:pdf,doc,docx',
            'doc_tambahan' => 'nullable|file|mimes:pdf,doc,docx',
            'domisili' => 'required|string',
        ]);

        $user = Auth::user();

        $applicant = $user->applicant ?? new Applicant(['user_id' => $user->id]);

        // Upload CV jika ada file baru
        if ($request->hasFile('cv')) {
            if ($applicant->cv) {
                Storage::delete($applicant->cv);
            }
            $applicant->cv = $request->file('cv')->store('cv_files');
        }

        // Upload doc tambahan jika ada
        if ($request->hasFile('doc_tambahan')) {
            if ($applicant->doc_tambahan) {
                Storage::delete($applicant->doc_tambahan);
            }
            $applicant->doc_tambahan = $request->file('doc_tambahan')->store('doc_tambahan_files');
        }

        $applicant->nik = $request->nik;
        $applicant->no_telp = $request->no_telp;
        $applicant->tpt_lahir = $request->tpt_lahir;
        $applicant->tgl_lahir = $request->tgl_lahir;
        $applicant->alamat_ktp = $request->alamat_ktp;
        $applicant->pendidikan = $request->pendidikan;
        $applicant->domisili = $request->domisili;

        $applicant->save();

        return redirect()->route('lowongan.index')->with('success', 'Biodata berhasil disimpan.');
    }
}

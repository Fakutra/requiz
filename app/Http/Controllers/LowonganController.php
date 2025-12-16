<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use Illuminate\Validation\ValidationException;
use App\Models\Batch;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class LowonganController extends Controller
{
    public function index(Request $request)
    {
        $q   = trim($request->input('q', ''));
        $edu = $request->input('edu', ''); // contoh: 'SMA/Sederajat', 'D3', 'D4', 'S1', 'S2', 'S3'

        // Ambil semua posisi yang aktif dan batch-nya aktif
        $positions = Position::query()
            ->withCount('applicants')
            ->where('status', 'Active')
            ->whereHas('batch', function ($q) {
                $q->where('status', 'Active');
            })
            // 🔍 Filter pencarian teks
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'ILIKE', "%{$q}%")
                    ->orWhere('slug', 'ILIKE', "%{$q}%")
                    ->orWhere('description', 'ILIKE', "%{$q}%");
                });
            })
            // 🎓 Filter jenjang pendidikan minimum
            ->when($edu, function ($query) use ($edu) {
                $query->where('pendidikan_minimum', $edu);
            })
            ->orderBy('id', 'asc')
            ->paginate(9)
            ->withQueryString(); // supaya query ?q=&edu= tetap terbawa di pagination

        return view('joblist', compact('positions', 'q', 'edu'));
    }

    public function store(Request $request, Position $position)
    {
        $user = auth()->user();
        if (!$user) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated'], 401)
                : redirect()->route('login');
        }

        $batchId = $position->batch_id;
        $profile = $user->profile;

        // ✅ VALIDASI PROFILE (biar 422, bukan 500)
        validator($profile?->toArray() ?? [], [
            'identity_num' => 'required|string|size:16',
            'phone_number' => 'required|string|max:20',
            'birthplace'   => 'required|string|max:255',
            'birthdate'    => 'required|date',
            'address'      => 'required|string',
        ], [
            'identity_num.required' => 'NIK pada Profile belum diisi.',
            'identity_num.size'     => 'NIK harus 16 digit.',
            'phone_number.required' => 'Nomor telepon pada Profile belum diisi.',
            'birthplace.required'   => 'Tempat lahir pada Profile belum diisi.',
            'birthdate.required'    => 'Tanggal lahir pada Profile belum diisi.',
            'address.required'      => 'Alamat KTP pada Profile belum diisi.',
        ])->validate();

        // ✅ CEK sekali per batch
        if (Applicant::where('user_id', $user->id)->where('batch_id', $batchId)->exists()) {
            $msg = 'Anda sudah melamar di batch ini. Anda hanya boleh melamar satu posisi per batch.';
            return $request->expectsJson()
                ? response()->json(['message' => $msg], 409)
                : redirect()->route('lowongan.index')->withErrors(['error' => $msg]);
        }

        // ✅ Cek kuota
        if ($position->applicants()->count() >= $position->quota) {
            $msg = 'Maaf, kuota untuk posisi ini sudah penuh.';
            return $request->expectsJson()
                ? response()->json(['message' => $msg], 409)
                : redirect()->route('lowongan.index')->withErrors(['error' => $msg]);
        }

        // ✅ VALIDASI FORM USER (biar 422, bukan 500)
        $validated = $request->validate([
            'pendidikan'      => 'required|in:SMA/Sederajat,D1,D2,D3,D4,S1,S2,S3',
            'universitas'     => 'required|string|max:255',
            'jurusan'         => 'required|string|max:255',
            'thn_lulus'       => 'required|digits:4',
            'skills'          => 'array',
            'ekspektasi_gaji' => 'required|numeric|min:0|max:100000000',
            'cv_document'     => 'required|file|mimes:pdf|max:512',  // 500 KB
            'doc_tambahan'    => 'nullable|file|mimes:pdf|max:5120',  // 5 MB
            'agreed'          => 'accepted',
        ], [
            'pendidikan.required'      => 'Jenjang pendidikan wajib dipilih.',
            'universitas.required'     => 'Universitas wajib diisi.',
            'jurusan.required'         => 'Jurusan wajib diisi.',
            'thn_lulus.required'       => 'Tahun lulus wajib diisi (4 digit).',
            'ekspektasi_gaji.required' => 'Ekspektasi gaji wajib diisi.',
            'cv_document.required'     => 'CV wajib diunggah.',
            'cv_document.mimes'        => 'CV harus berformat PDF.',
            'cv_document.max'          => 'CV maksimal 1 MB.',
            'doc_tambahan.mimes'       => 'Dokumen tambahan harus PDF.',
            'doc_tambahan.max'         => 'Dokumen tambahan maksimal 5 MB.',
            'agreed.accepted'          => 'Centang persetujuan syarat & ketentuan.',
        ]);

        // normalisasi gaji
        $validated['ekspektasi_gaji'] = (int) str_replace(['.', ',', ' '], '', $validated['ekspektasi_gaji']);

        // skills → string (support "Lainnya")
        $skills = $request->input('skills', []);
        if (in_array('Lainnya', $skills, true) && $request->filled('other_skill')) {
            $skills[array_search('Lainnya', $skills, true)] = (string) $request->input('other_skill');
        }
        $validated['skills'] = count($skills) ? implode(', ', $skills) : '-';

        // ====== SIMPAN FILE & CREATE (baru pakai try/catch) ======
        try {
            // simpan file
            $validated['cv_document'] = $request->file('cv_document')
                ->store("cv-applicant/{$user->id}", 'public');

            if ($request->hasFile('doc_tambahan')) {
                $validated['doc_tambahan'] = $request->file('doc_tambahan')
                    ->store("doc-applicant/{$user->id}", 'public');
            } else {
                unset($validated['doc_tambahan']);
            }

            // map profile → kolom applicants
            $applicantData = array_merge($validated, [
                'user_id'     => $user->id,
                'position_id' => $position->id,
                'batch_id'    => $batchId,

                'name'        => $user->name,
                'email'       => $user->email,

                'identity_num'=> $profile->identity_num,
                'phone_number'=> $profile->phone_number,
                'birthplace'  => $profile->birthplace,
                'birthdate'   => $profile->birthdate,
                'address'     => $profile->address,
            ]);

            Applicant::create($applicantData);

            return $request->expectsJson()
                ? response()->json(['message' => 'OK'], 200)
                : redirect()->route('history.index')->with('success', 'Selamat! Lamaran Anda telah berhasil dikirim.');
        } catch (\Throwable $e) {
            Log::error('Error apply job', [
                'msg' => $e->getMessage(),
                'user_id' => $user->id ?? null,
                'position_id' => $position->id ?? null,
            ]);

            // kalau via fetch: kasih JSON 500
            return $request->expectsJson()
                ? response()->json(['message' => 'Terjadi kesalahan saat mengirim lamaran.'], 500)
                : back()->withErrors(['error' => 'Terjadi kesalahan saat mengirim lamaran.']);
        }
    }

    public function show(Position $position)
    {
        $position->load('batch');

        $currentApplicantsCount = Applicant::where('position_id', $position->id)->count();
        $quota = $position->quota;
        $isQuotaFull = ($quota > 0) && ($currentApplicantsCount >= $quota);

        $appliedBatchIds = Applicant::where('user_id', auth()->id())
            ->pluck('batch_id')
            ->toArray();

        // Ambil user beserta profil-nya
        $user = User::with('profile')->find(auth()->id());

        return view('jobdetail', compact('position', 'appliedBatchIds', 'user', 'isQuotaFull'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
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
        try {
            $user = auth()->user();
            $batchId = $position->batch_id;

            // 🔍 Cek apakah user sudah pernah melamar di batch yang sama
            $alreadyApplied = Applicant::where('user_id', $user->id)
                ->where('batch_id', $batchId)
                ->exists();

            if ($alreadyApplied) {
                return redirect()->route('lowongan.index')
                    ->withErrors(['error' => 'Anda sudah melamar di batch ini. Anda hanya boleh melamar satu posisi per batch.']);
            }

            // 🔍 Cek kuota posisi
            if ($position->applicants()->count() >= $position->quota) {
                return redirect()->route('lowongan.index')
                    ->withErrors(['error' => 'Maaf, kuota untuk posisi ini sudah penuh.']);
            }

            // ✅ Validasi form input
            $validated = $request->validate([
                'pendidikan'      => 'required|in:SMA/Sederajat,D1,D2,D3,D4,S1,S2,S3',
                'universitas'     => 'required|string|max:255',
                'jurusan'         => 'required|string|max:255',
                'thn_lulus'       => 'required|digits:4',
                'skills'          => 'array',
                'ekspektasi_gaji' => 'required|numeric|min:0|max:100000000',
                'cv_document'     => 'required|file|mimes:pdf|max:3072',
                'doc_tambahan'    => 'nullable|file|mimes:pdf|max:5120',
                'agreed'          => 'accepted',
            ], [
                'agreed.accepted' => 'Harap centang kotak persetujuan syarat & ketentuan.',
            ]);

            // 💼 Bersihkan format angka gaji
            $validated['ekspektasi_gaji'] = (int) str_replace(['.', ',', ' '], '', $validated['ekspektasi_gaji']);

            // 📂 Upload file CV dan dokumen tambahan
            $validated['cv_document'] = $request->file('cv_document')->store("cv-applicant/{$user->id}", 'public');

            if ($request->hasFile('doc_tambahan')) {
                $validated['additional_doc'] = $request->file('doc_tambahan')
                    ->store("doc-applicant/{$user->id}", 'public');
            }

            // 🧠 Tangani daftar skill
            $skills = $request->input('skills', []);
            if (in_array('Lainnya', $skills, true) && $request->filled('other_skill')) {
                $skills[array_search('Lainnya', $skills, true)] = $request->input('other_skill');
            }
            $validated['skills'] = !empty($skills) ? implode(', ', $skills) : null;

            // 🧾 Tambahkan relasi penting
            $validated['user_id']     = $user->id;
            $validated['position_id'] = $position->id;
            $validated['batch_id']    = $batchId;

            // 🧩 Kolom 'status' otomatis 'Seleksi Administrasi' dari migration (default)
            Applicant::create($validated);

            return redirect()->route('history.index')->with('success', 'Selamat! Lamaran Anda telah berhasil dikirim.');
        } catch (Throwable $e) {
            Log::error('Error apply job: '.$e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat mengirim lamaran.']);
        }
    }

    public function show(Position $position)
    {
        $position->load('batch');

        $appliedBatchIds = Applicant::where('user_id', auth()->id())
            ->pluck('batch_id')
            ->toArray();

        // Ambil user beserta profil-nya
        $user = User::with('profile')->find(auth()->id());

        return view('jobdetail', compact('position', 'appliedBatchIds', 'user'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Position;
use App\Models\User;
use App\Services\PdfWatermarkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LowonganController extends Controller
{
    public function index(Request $request)
    {
        return view('joblist');
    }

    public function store(Request $request, Position $position)
    {
        $user = Auth::user();
        if (!$user) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated'], 401)
                : redirect()->route('login');
        }

        $profile = $user->profile;
        $batchId = $position->batch_id;

        /**
         * ===============================
         * VALIDASI PROFILE
         * ===============================
         */
        validator($profile?->toArray() ?? [], [
            'identity_num' => 'required|string|size:16',
            'phone_number' => 'required|string|max:20',
            'birthplace'   => 'required|string|max:255',
            'birthdate'    => 'required|date',
            'address'      => 'required|string',
        ])->validate();

        /**
         * ===============================
         * CEK SATU LAMARAN PER BATCH
         * ===============================
         */
        if (Applicant::where('user_id', $user->id)->where('batch_id', $batchId)->exists()) {
            $msg = 'Anda sudah melamar di batch ini. Anda hanya boleh melamar satu posisi per batch.';
            return $request->expectsJson()
                ? response()->json(['message' => $msg], 409)
                : back()->withErrors(['error' => $msg]);
        }

        /**
         * ===============================
         * CEK KUOTA
         * ===============================
         */
        if ($position->applicants()->count() >= $position->quota) {
            $msg = 'Maaf, kuota untuk posisi ini sudah penuh.';
            return $request->expectsJson()
                ? response()->json(['message' => $msg], 409)
                : back()->withErrors(['error' => $msg]);
        }

        /**
         * ===============================
         * VALIDASI FORM
         * ===============================
         */
        $validated = $request->validate([
            'pendidikan'      => 'required|in:SMA/Sederajat,D1,D2,D3,D4,S1,S2,S3',
            'universitas'     => 'required|string|max:255',
            'jurusan'         => 'required|string|max:255',
            'thn_lulus'       => 'required|digits:4',
            'skills'          => 'array',
            'ekspektasi_gaji' => 'required|numeric|min:0|max:100000000',
            'cv_document'     => 'required|file|mimes:pdf|max:512',
            'doc_tambahan'    => 'nullable|file|mimes:pdf|max:5120',
            'agreed'          => 'accepted',
        ]);

        /**
         * ===============================
         * NORMALISASI DATA
         * ===============================
         */
        $validated['ekspektasi_gaji'] = (int) str_replace(['.', ',', ' '], '', $validated['ekspektasi_gaji']);

        $skills = $request->input('skills', []);
        if (in_array('Lainnya', $skills, true) && $request->filled('other_skill')) {
            $skills[array_search('Lainnya', $skills, true)] = (string) $request->input('other_skill');
        }
        $validated['skills'] = count($skills) ? implode(', ', $skills) : '-';

        try {
            /**
             * ==================================================
             * WATERMARK CV
             * ==================================================
             */
            Storage::disk('public')->makeDirectory("cv-applicant/{$user->id}");

            $cvTmpPath = $request->file('cv_document')->store('tmp', 'local');
            $finalCvPath = "cv-applicant/{$user->id}/cv_{$position->id}.pdf";

            PdfWatermarkService::watermark(
                storage_path("app/{$cvTmpPath}"),
                storage_path("app/public/{$finalCvPath}"),
                $user->name
            );

            Storage::delete($cvTmpPath);
            $validated['cv_document'] = $finalCvPath;

            /**
             * ==================================================
             * WATERMARK DOKUMEN TAMBAHAN (OPTIONAL)
             * ==================================================
             */
            if ($request->hasFile('doc_tambahan')) {
                Storage::disk('public')->makeDirectory("doc-applicant/{$user->id}");

                $docTmpPath = $request->file('doc_tambahan')->store('tmp', 'local');
                $finalDocPath = "doc-applicant/{$user->id}/doc_{$position->id}.pdf";

                PdfWatermarkService::watermark(
                    storage_path("app/{$docTmpPath}"),
                    storage_path("app/public/{$finalDocPath}"),
                    $user->name
                );

                Storage::delete($docTmpPath);
                $validated['doc_tambahan'] = $finalDocPath;
            }

            /**
             * ==================================================
             * CREATE APPLICANT
             * ==================================================
             */
            Applicant::create(array_merge($validated, [
                'user_id'      => $user->id,
                'position_id'  => $position->id,
                'batch_id'     => $batchId,
                'name'         => $user->name,
                'email'        => $user->email,
                'identity_num' => $profile->identity_num,
                'phone_number' => $profile->phone_number,
                'birthplace'   => $profile->birthplace,
                'birthdate'    => $profile->birthdate,
                'address'      => $profile->address,
            ]));

            return $request->expectsJson()
                ? response()->json(['message' => 'OK'], 200)
                : redirect()->route('history.index')->with('success', 'Selamat! Lamaran Anda telah berhasil dikirim.');

        } catch (\Throwable $e) {
            Log::error('Apply Job + Watermark Error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'user_id' => $user->id,
                'position_id' => $position->id,
            ]);

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

        $user = User::with('profile')->find(auth()->id());

        return view('jobdetail', compact(
            'position',
            'appliedBatchIds',
            'user',
            'isQuotaFull'
        ));
    }
}

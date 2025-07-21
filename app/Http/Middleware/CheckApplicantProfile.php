<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckApplicantProfile
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $applicant = $user->applicant;

        // Cek kalau biodata belum ada / belum lengkap
        if (
            !$applicant ||
            !$applicant->nik ||
            !$applicant->no_telp ||
            !$applicant->tpt_lahir ||
            !$applicant->tgl_lahir ||
            !$applicant->alamat_ktp ||
            !$applicant->pendidikan ||
            !$applicant->cv ||
            !$applicant->domisili
        ) {
            return redirect()->route('applicant.create')->with('error', 'Harap lengkapi biodata terlebih dahulu.');
        }

        return $next($request);
    }
}

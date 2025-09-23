<?php
// app/Services/SelectionLogger.php

namespace App\Services;

use App\Models\SelectionLog;
use App\Models\Applicant;
use Illuminate\Support\Str;

class SelectionLogger
{
    /**
     * Tulis log keputusan untuk satu applicant di satu tahap.
     * $stage    : label tahap (mis. 'Tes Tulis')
     * $result   : 'lolos' | 'tidak_lolos'
     * $actorId  : optional, user id admin
     */
    public static function write(Applicant $applicant, string $stage, string $result, ?int $actorId = null): SelectionLog
    {
        // Normalisasi aman
        $stageKey = Str::slug($stage);
        $stageKey = str_replace('seleksi-', '', $stageKey); // konsisten seperti rekap

        return SelectionLog::create([
            'applicant_id' => $applicant->id,
            'stage'        => $stage,
            'stage_key'    => $stageKey,
            'result'       => $result, // pastikan sudah divalidasi in: lolos, tidak_lolos
            'position_id'  => $applicant->position_id,
            'jurusan'      => $applicant->jurusan,
            'acted_by'     => $actorId,
        ]);
    }
}

<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Catat aktivitas user ke tabel activity_logs
     */
    public static function log(string $action, string $module, ?string $description = null, ?string $target = null)
    {
        try {
            ActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => $action,
                'module'     => $module,
                'description'=> $description,
                'target'     => $target,
                'ip_address' => self::getClientIp(),
            ]);
        } catch (\Throwable $e) {
            \Log::error('Gagal mencatat log aktivitas: '.$e->getMessage());
        }
    }

    /**
     * Catat perubahan (update) antara data lama dan baru
     */
    public static function logUpdate(string $module, $model, array $oldData, array $newData)
    {
        $changes = [];
        foreach ($newData as $key => $newValue) {
            $oldValue = $oldData[$key] ?? null;
            if ($oldValue != $newValue) {
                $changes[] = "{$key}: '{$oldValue}' → '{$newValue}'";
            }
        }

        if (!empty($changes)) {
            // cek apakah model punya relasi applicant
            $applicantName = method_exists($model, 'applicant') && $model->applicant
                ? $model->applicant->name
                : 'Data Tidak Dikenal';

            $desc = Auth::user()->name
                  ." memperbarui data pada {$module} untuk peserta {$applicantName} "
                  ."— ".implode(', ', $changes);

            self::log(
                'update',
                $module,
                $desc,
                "Peserta: {$applicantName}"
            );
        }
    }

    /**
     * Ambil IP Address user (support proxy/reverse proxy)
     */
    private static function getClientIp(): ?string
    {
        $ip = Request::ip();

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $forwarded = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($forwarded[0]);
        }

        return $ip;
    }
}

<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;


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
            Log::error('Gagal mencatat log aktivitas: '.$e->getMessage());
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

            // ✅ Normalisasi Carbon / datetime
            if ($oldValue instanceof \Carbon\Carbon) {
                $oldValue = $oldValue->format('Y-m-d H:i:s');
            }
            if ($newValue instanceof \Carbon\Carbon) {
                $newValue = $newValue->format('Y-m-d H:i:s');
            }

            // ✅ Normalisasi angka (float/int) biar tidak gagal deteksi
            if (is_numeric($oldValue) && is_numeric($newValue)) {
                $oldValue = (float)$oldValue;
                $newValue = (float)$newValue;
            }

            // ✅ Normalisasi null & spasi
            $oldValue = $oldValue === null ? '' : trim((string)$oldValue);
            $newValue = $newValue === null ? '' : trim((string)$newValue);

            // ✅ Bandingkan setelah normalisasi
            if ($oldValue !== $newValue) {
                $changes[] = "{$key}: '{$oldValue}' → '{$newValue}'";
            }
        }

        if (empty($changes)) {
            return; // tidak ada perbedaan
        }

        // ✅ Nama entitas untuk log
        $modelName =
            ($model->name ?? null)
            ?: ($model->title ?? null)
            ?: ($model->id ?? '(unknown)');

        $desc = (Auth::user()->name ?? 'System')
            ." memperbarui data pada {$module} '{$modelName}' — "
            .implode(', ', $changes);

        self::log('update', $module, $desc, "{$module}: {$modelName}");
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

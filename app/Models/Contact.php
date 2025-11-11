<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Contact extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'narahubung', 'email', 'phone', 'jam_operasional', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Scope ambil kontak aktif
    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    // Accessor: auto link WhatsApp
    public function getWaNumberAttribute(): ?string
    {
        $raw = (string) $this->phone;
        if ($raw === '') return null;

        // buang non-digit
        $n = preg_replace('/\D+/', '', $raw);

        // 0xxxxxxxx -> 62xxxxxxxx
        if (Str::startsWith($n, '0')) {
            $n = '62'.ltrim($n, '0');
        }

        // 6208xxxx -> 628xxxx  (kadang user ngetik "62" + "0")
        $n = preg_replace('/^620+/', '62', $n);

        // kalau belum ada 62 sama sekali, tambahin
        if (!Str::startsWith($n, '62')) {
            $n = '62'.$n;
        }
        return $n;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = ['position', 'quota', 'tanggal'];

    protected static function booted()
    {
        static::creating(function ($position) {
            $position->tanggal = now(); // Atur tanggal saat ini
        });
    }
}

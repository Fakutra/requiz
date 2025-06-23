<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'position_id',
        'name',
        'email',
        'nik',
        'no_telp',
        'tpt_lahir',
        'tgl_lahir',
        'alamat',
        'pendidikan',
        'universitas',
        'cv',
        'doc_tambahan',
    ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class); // Jika pelamar login
    }

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    // public function position()
    // {
    //     return $this->belongsTo(Position::class);
    // }
}

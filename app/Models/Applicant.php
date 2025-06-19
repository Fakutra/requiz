<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    use HasFactory;

    protected $fillable = [
        'nik',
        'no_telp',
        'tpt_lahir',
        'tgl_lahir',
        'alamat',
        'pendidikan',
        'cv',
        'doc_tambahan',
        'status',
        'position_id',
        'user_id'
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

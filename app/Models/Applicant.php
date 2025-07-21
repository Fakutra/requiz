<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Applicant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'batch_id',
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
        'jurusan',
        'thn_lulus',
        'skills',
        'cv_document',
        'status',
    ];

    // Di model Applicant
    // protected $appends = ['pendidikan', 'universitas']; // Sesuaikan dengan kolom actual

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class); // Jika pelamar login
    }

    public function testResults()
    {
        return $this->hasMany(TestResult::class);
    }

    
    public function getAgeAttribute()
    {
        return Carbon::parse($this->tgl_lahir)->age;
    }
    public function getCurrentStageAttribute()
    {
        $status = $this->status;
        if (str_contains($status, 'Administrasi')) return 'Seleksi Administrasi';
        if (str_contains($status, 'Tes Tulis')) return 'Seleksi Tes Tulis';
        if (str_contains($status, 'Technical Test')) return 'Technical Test';
        if (str_contains($status, 'Interview')) return 'Interview';
        return 'Tahap Tidak Dikenal';
    }
}


// public function getRouteKeyName()
// {
//     return 'slug';
// }


// public function user()
// {
//     return $this->belongsTo(User::class);
// }

// public function position()
// {
//     return $this->belongsTo(Position::class);
// }
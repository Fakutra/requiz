<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Applicant extends Model
{
    protected $fillable = [
<<<<<<< HEAD
        'user_id',
        'batch_id',
        'position_id',
        'pendidikan',
        'universitas',
        'jurusan',
        'thn_lulus',
        'skills',
        'cv_document',
        'status',
        'additional_doc',
=======
        'name','email','nik','no_telp','tpt_lahir','tgl_lahir','alamat',
        'pendidikan','universitas','jurusan','thn_lulus','status',
        'cv_document','position_id','batch_id',
>>>>>>> origin/main
    ];

    protected $dates = ['tgl_lahir'];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    // dipakai di view -> {{ $applicant->age }}
    public function getAgeAttribute(): ?int
    {
        if (!$this->tgl_lahir) return null;
        return Carbon::parse($this->tgl_lahir)->age;
    }
<<<<<<< HEAD

    public function getCurrentStageAttribute()
    {
        $status = $this->status;
        if (str_contains($status, 'Administrasi')) return 'Seleksi Administrasi';
        if (str_contains($status, 'Tes Tulis')) return 'Seleksi Tes Tulis';
        if (str_contains($status, 'Technical Test')) return 'Technical Test';
        if (str_contains($status, 'Interview')) return 'Interview';
        return 'Tahap Tidak Dikenal';
    }

    public function emailLogs()
    {
        return $this->hasMany(\App\Models\EmailLog::class);
    }
    
=======
>>>>>>> origin/main
}

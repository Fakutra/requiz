<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Applicant extends Model
{
    protected $fillable = [
        'name','email','nik','no_telp','tpt_lahir','tgl_lahir','alamat',
        'pendidikan','universitas','jurusan','thn_lulus','status',
        'cv_document','position_id','batch_id',
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
}

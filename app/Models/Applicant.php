<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    protected $fillable = [
        'user_id', 'nik', 'no_telp', 'tpt_lahir', 'tgl_lahir', 'alamat_ktp', 'pendidikan', 'cv', 'doc_tambahan', 'domisili'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offering extends Model
{
    // field yang bisa diisi mass-assignment
    protected $fillable = [
        'applicant_id',
        'position',
        'field_id',
        'sub_field_id',
        'job_id',
        'seksi_id',
        'gaji',
        'uang_makan',
        'uang_transport',
        'kontrak_mulai',
        'kontrak_selesai',
        'link_pkwt',
        'link_berkas',
        'link_form_pelamar',
        'response_deadline',
        'responded_at',
    ];

    protected $casts = [
        'kontrak_mulai' => 'date',
        'kontrak_selesai' => 'date',
        'response_deadline' => 'datetime',
        'responded_at'      => 'datetime',
    ];

    public function isExpired(): bool
    {
        return $this->response_deadline
            && now()->greaterThan($this->response_deadline)
            && !$this->responded_at;
    }


    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    // relasi ke Applicant
    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    // relasi ke Job
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    // relasi ke Seksi
    public function seksi()
    {
        return $this->belongsTo(Seksi::class);
    }

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

   public function subfield()
{
    return $this->belongsTo(SubField::class, 'sub_field_id'); // ✅
}
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offering extends Model
{
    // field yang bisa diisi mass-assignment
    protected $fillable = [
        'applicant_id','position','division_id','job_id','placement_id',
        'gaji','uang_makan','uang_transport',
        'kontrak_mulai','kontrak_selesai',
        'link_pkwt','link_berkas','link_form_pelamar'
    ];

    protected $casts = [
        'kontrak_mulai' => 'date',
        'kontrak_selesai' => 'date',
    ];


    // relasi ke Applicant
    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    // relasi ke Division
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    // relasi ke Job
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    // relasi ke Placement
    public function placement()
    {
        return $this->belongsTo(Placement::class);
    }
}

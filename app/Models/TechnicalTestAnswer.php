<?php

// app/Models/TechnicalTestAnswer.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechnicalTestAnswer extends Model
{
    // Jika kamu memaksa nama tabel singular "technical_test_answer",
    // uncomment baris di bawah ini:
    // protected $table = 'technical_test_answer';

    protected $fillable = [
        'technical_test_schedule_id',
        'applicant_id',
        'answer_path',
        'screen_record_url',
        'submitted_at',
        'score',
        'keterangan',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'score'        => 'decimal:2',
    ];

    public function schedule()  
    { 
        return $this->belongsTo(TechnicalTestSchedule::class, 'technical_test_schedule_id'); 
    }
    public function applicant() 
    { 
        return $this->belongsTo(Applicant::class); 
    }

    public function getAnswerUrlAttribute()
    {
        return $this->answer_path ? asset('storage/'.$this->answer_path) : null;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterviewResult extends Model
{
    protected $fillable = [
        'applicant_id', 'user_id',
        'poin_kepribadian', 'poin_wawasan',
        'poin_gestur', 'poin_cara_bicara',
        'score', 'potencial', 'note'
    ];

    public function applicant() {
        return $this->belongsTo(Applicant::class);
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}

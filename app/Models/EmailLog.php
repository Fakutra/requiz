<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $table = 'email_logs';

    protected $fillable = [
        'applicant_id',
        'email',
        'stage',
        'subject',
        'success',
        'error',
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
}

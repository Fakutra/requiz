<?php

// app/Models/SelectionLog.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SelectionLog extends Model
{
    protected $fillable = [
        'applicant_id','stage','stage_key','result',
        'position_id','jurusan','acted_by',
    ];

    public function applicant() { return $this->belongsTo(Applicant::class); }
    public function position()  { return $this->belongsTo(Position::class); }
    public function actor()     { return $this->belongsTo(User::class, 'acted_by'); }
}

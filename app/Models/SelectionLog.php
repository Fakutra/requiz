<?php
// app/Models/SelectionLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SelectionLog extends Model
{
    protected $fillable = [
        'applicant_id','stage','stage_key','result',
        'position_id','jurusan','acted_by',
    ];

    public function applicant(): BelongsTo { return $this->belongsTo(Applicant::class); }
    public function position(): BelongsTo { return $this->belongsTo(Position::class); }
    public function actor(): BelongsTo    { return $this->belongsTo(User::class, 'acted_by'); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'applicant_id',
        'test_id',
        'started_at',
        'finished_at',
        'score',
    ];

    protected $casts = [
        'started_at'  => 'datetime',
        'finished_at' => 'datetime',
        'score'       => 'decimal:2', // opsional
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function sectionResults()
    {
        return $this->hasMany(TestSectionResult::class);
    }
}

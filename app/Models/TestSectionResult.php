<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestSectionResult extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'test_result_id', // Tambahkan baris ini
        'test_section_id', // Tambahkan baris ini
        'started_at',
        'finished_at',
        'score'
    ];

    public function testResult()
    {
        return $this->belongsTo(TestResult::class);
    }

    public function testSection()
    {
        return $this->belongsTo(TestSection::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}

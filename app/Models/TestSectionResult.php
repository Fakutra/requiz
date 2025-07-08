<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestSectionResult extends Model
{
    use HasFactory;

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

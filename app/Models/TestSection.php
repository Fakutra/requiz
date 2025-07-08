<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestSection extends Model
{
    use HasFactory;

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function bundle()
    {
        return $this->belongsTo(QuestionBundle::class, 'question_bundle_id');
    }

    public function sectionResults()
    {
        return $this->hasMany(TestSectionResult::class);
    }
}

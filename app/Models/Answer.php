<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    public function sectionResult()
    {
        return $this->belongsTo(TestSectionResult::class, 'test_section_result_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}

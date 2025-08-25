<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'applicant_id',
        'question_id',
        'test_section_id',        // <-- Wajib ada!
        'test_result_id',
        'test_section_result_id',
        'answer',
        'score',                  // pakai 'score', jangan 'is_correct' (tidak ada di DB)
    ];

    public function sectionResult()
    {
        return $this->belongsTo(TestSectionResult::class, 'test_section_result_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'category',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'option_e',
        'point_a',
        'point_b',
        'point_c',
        'point_d',
        'point_e',
        'answer',
        'image_path'
    ];

    public function bundles()
    {
        return $this->belongsToMany(QuestionBundle::class, 'bundle_question');
    }

    public function Answers()
    {
        return $this->hasMany(Answer::class);
    }
}
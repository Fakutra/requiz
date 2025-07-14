<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    public function bundles()
    {
        return $this->belongsToMany(QuestionBundle::class, 'bundle_question');
    }

    public function Answers()
    {
        return $this->hasMany(Answer::class);
    }
}
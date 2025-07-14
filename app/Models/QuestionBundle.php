<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionBundle extends Model
{
    use HasFactory;

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'bundle_question');
    }

    public function sections()
    {
        return $this->hasMany(TestSection::class);
    }
}

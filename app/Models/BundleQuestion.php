<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BundleQuestion extends Model
{
    use HasFactory;

    public function questionBundle() {
        return $this->belongsTo(QuestionBundle::class);
    }
    public function question() {
        return $this->belongsTo(Question::class);
    }

}

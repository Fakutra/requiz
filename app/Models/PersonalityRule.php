<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalityRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'min_percentage', 'max_percentage', 'score_value',
    ];
}

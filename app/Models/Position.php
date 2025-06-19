<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'quota',
        'status',
        'description',
    ];

    public function user()
    {
        return $this->hasMany(User::class);
    }

    public function applicant()
    {
        return $this->hasMany(Applicant::class);
    }
}

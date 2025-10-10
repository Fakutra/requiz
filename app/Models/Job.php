<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $fillable = ['name'];

    // Relasi ke Offering
    public function offerings()
    {
        return $this->hasMany(Offering::class);
    }
}

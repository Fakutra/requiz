<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    protected $fillable = ['name'];

    public function subFields()
    {
        return $this->hasMany(SubField::class);
    }

    public function offerings()
    {
        return $this->hasMany(Offering::class);
    }
}


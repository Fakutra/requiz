<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubField extends Model
{
    protected $fillable = ['field_id', 'name'];

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    public function seksi()
    {
        return $this->hasMany(Seksi::class);
    }

    public function offerings()
    {
        return $this->hasMany(Offering::class);
    }
}

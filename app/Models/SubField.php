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

    public function seksis()
    {
        return $this->hasMany(Seksi::class, 'sub_field_id');
    }

    public function offerings()
    {
        return $this->hasMany(Offering::class);
    }
}

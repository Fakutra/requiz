<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seksi extends Model
{
    protected $table = 'seksi';

    protected $fillable = ['sub_field_id', 'name'];

    public function subField()
    {
        return $this->belongsTo(SubField::class, 'sub_field_id');
    }

    public function offerings()
    {
        return $this->hasMany(Offering::class);
    }
}

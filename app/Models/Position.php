<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory, Sluggable;

    protected $fillable = [
        'batch_id',
        'name',
        'slug',
        'quota',
        'status',
        'description',
    ];

    public function user()
    {
        return $this->hasMany(User::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function applicants()
    {
        return $this->hasMany(Applicant::class);
    }
    
    public function test()
    {
        return $this->hasOne(Test::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
                'onUpdate' => true,
            ]
        ];
    }
}

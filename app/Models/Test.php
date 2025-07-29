<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Test extends Model
{
    use HasFactory, Sluggable;

    protected $fillable = [
        'name',
        'slug',
        'position_id',
        'test_date',
    ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function section()
    {
        return $this->hasMany(TestSection::class);
    }

    public function results()
    {
        return $this->hasMany(TestResult::class);
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

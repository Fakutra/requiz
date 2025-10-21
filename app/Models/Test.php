<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Test extends Model
{
    use HasFactory, Sluggable;

    protected $fillable = [
        'position_id',
        'name',
        'slug',
        'nilai_minimum',   // ✅ tambahkan ini
        'test_date',
        'test_closed',
        'test_end',
    ];

    protected $casts = [
        'nilai_minimum' => 'decimal:2',  // ✅ supaya otomatis dikonversi ke float 2 desimal
        'test_date'     => 'datetime',
        'test_closed'   => 'datetime',
        'test_end'      => 'datetime',
    ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function sections()
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

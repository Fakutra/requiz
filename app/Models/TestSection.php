<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class TestSection extends Model
{
    use HasFactory, Sluggable;

    protected $fillable = [
        'test_id',
        'name',
        'slug',
        'question_bundle_id',
        'duration_minutes',
        'shuffle_questions',
        'shuffle_options',
        'order', // Pastikan kolom ini ada di database Anda
    ];

    protected $casts = [
        'shuffle_questions' => 'boolean',
        'shuffle_options' => 'boolean',
    ];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function questionBundle()
    {
        return $this->belongsTo(QuestionBundle::class);
    }

    public function sectionResults()
    {
        return $this->hasMany(TestSectionResult::class);
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
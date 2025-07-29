<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable; // PASTIKAN ANDA SUDAH MENGIMPORT INI

class TestSection extends Model
{
    // Tambahkan use Sluggable
    use HasFactory, Sluggable;

    // Sesuaikan $fillable dengan skema database
    protected $fillable = [
        'test_id',
        'name', // diubah dari 'title'
        'slug',
        'type',
        'question_bundle_id',
        'duration_minutes', // diubah dari 'duration'
        'shuffle_questions',
        'shuffle_options',
    ];

    /**
     * Casting tipe data untuk boolean.
     */
    protected $casts = [
        'shuffle_questions' => 'boolean',
        'shuffle_options' => 'boolean',
    ];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function bundle()
    {
        return $this->belongsTo(QuestionBundle::class, 'question_bundle_id');
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


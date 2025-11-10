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
        'order',
    ];

    protected $casts = [
    'shuffle_questions' => 'boolean',
    'shuffle_options'   => 'boolean',
    'duration_minutes'  => 'integer',   // ✅ tambah ini
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

    /**
     * ✅ Hitung jumlah soal dalam bundle section ini.
     */
    public function getQuestionsCountAttribute(): int
    {
        if ($this->relationLoaded('questionBundle') && $this->questionBundle) {
            return (int) $this->questionBundle->questions()->count();
        }
        return $this->questionBundle
            ? (int) $this->questionBundle->questions()->count()
            : 0;
    }
}

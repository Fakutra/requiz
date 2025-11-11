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
        'nilai_minimum',
        'test_date',
        'test_closed',
        'test_end',
        'intro', // ✅ agar bisa diupdate langsung
    ];

    protected $casts = [
        'nilai_minimum' => 'decimal:2',
        'test_date'     => 'datetime',
        'test_closed'   => 'datetime',
        'test_end'      => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
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

    /**
     * ✅ Relasi ke pertanyaan (soal) lewat TestSection.
     * Ini untuk menghitung jumlah total soal lintas section.
     */
    public function questions()
    {
        return $this->hasManyThrough(
            Question::class,
            TestSection::class,
            'test_id',    // FK di TestSection yang mengarah ke Test
            'section_id', // FK di Question yang mengarah ke TestSection
            'id',         // PK di Test
            'id'          // PK di TestSection
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Slug Settings
    |--------------------------------------------------------------------------
    */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source'   => 'name',
                'onUpdate' => true,
            ]
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * ✅ Hitung total semua soal dalam quiz (semua section).
     */
    // ... (file Test.php lu yang sekarang)

    public function getTotalQuestionsAttribute(): int
    {
        // Kalau sections sudah diload, manfaatin accessor di TestSection biar hemat query
        if ($this->relationLoaded('sections')) {
            return (int) $this->sections->sum(fn ($s) => (int) $s->questions_count);
        }

        // Fallback: load sekalian bundle biar accessor questions_count tetap hemat
        $sections = $this->sections()->with('questionBundle')->get();
        return (int) $sections->sum(fn ($s) => (int) $s->questions_count);
    }

    public function getTotalDurationMinutesAttribute(): int
    {
        if ($this->relationLoaded('sections')) {
            return (int) $this->sections->sum('duration_minutes');
        }
        return (int) $this->sections()->sum('duration_minutes');
    }

    public function getTotalDurationHhmmAttribute(): string
    {
        $m  = (int) $this->total_duration_minutes;
        $hh = intdiv($m, 60);
        $mm = $m % 60;
        return sprintf('%02d:%02d', $hh, $mm);
    }

    public function getIntroRenderedAttribute(): string
    {
        $intro = (string) ($this->intro ?? '');
        if ($intro === '') return '';

        $map = [
            ':total_questions'        => (string) $this->total_questions,
            ':total_duration_minutes' => (string) $this->total_duration_minutes,
            ':total_duration_hhmm'    => (string) $this->total_duration_hhmm,
        ];

        return nl2br(strtr($intro, $map));
    }
}

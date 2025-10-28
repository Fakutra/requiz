<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'category',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'option_e',
        'point_a',
        'point_b',
        'point_c',
        'point_d',
        'point_e',
        'answer',
        'image_path'
    ];

    /**
     * Casting untuk jawaban multiple (misal "A,B,C" jadi array)
     */
    protected $casts = [
        'answer' => 'string', // tetap string untuk PG
    ];

    public function getAnswerArrayAttribute()
    {
        return $this->type === 'Multiple'
            ? explode(',', $this->answer)
            : [$this->answer];
    }

    /**
     * Accessor untuk mengambil URL gambar
     */
    public function getImageUrlAttribute()
    {
        return $this->image_path
            ? asset('storage/' . $this->image_path)
            : null;
    }

    /**
     * Scope search (opsional): memudahkan pencarian
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where('question', 'LIKE', "%{$keyword}%");
    }

    /**
     * Relasi ke bundle (Many to Many)
     */
    public function bundles()
    {
        return $this->belongsToMany(QuestionBundle::class, 'bundle_questions');
    }

    /**
     * Relasi ke jawaban user (One to Many)
     */
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}

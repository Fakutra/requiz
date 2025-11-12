<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory, Sluggable;

    protected $fillable = [
        'batch_id','name','slug','quota','pendidikan_minimum',
        'skills','requirements','majors','deadline','status','description'
    ];

    // cast agar Laravel otomatis simpan array sebagai JSON dan decode saat dibaca
    protected $casts = [
        'skills' => 'array',
        'requirements' => 'array',
        'majors' => 'array',
        'description' => 'array', // note: column name is description (store array here)
        'deadline' => 'date',
    ];

    // helper kecil (opsional) untuk get single-line preview
    public function getDescriptionPreviewAttribute()
    {
        $arr = $this->description;
        if (is_array($arr) && count($arr)) {
            return implode(' — ', array_slice($arr, 0, 3)); // contoh preview
        }
        if (is_string($this->description)) {
            return \Str::limit(strip_tags($this->description), 80);
        }
        return null;
    }

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

    // app/Models/Position.php
    public function technicalSchedules()
    {
        return $this->hasMany(\App\Models\TechnicalTestSchedule::class, 'position_id');
    }

    // app/Models/Position.php
    public function interviewSchedules()
    {
        return $this->hasMany(\App\Models\InterviewSchedule::class);
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

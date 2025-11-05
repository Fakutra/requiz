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
        'pendidikan_minimum',
        'description',
        'skills',
        'requirements',
        'majors',
        'deadline',
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

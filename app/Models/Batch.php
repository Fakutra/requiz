<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Batch extends Model
{
    use HasFactory, Sluggable;

    protected $fillable = [
        'name',
        'slug',
        'status',
        'start_date',
        'end_date',
    ];

    public function position()
    {
        return $this->hasMany(Position::class);
    }

    public function applicant()
    {
        return $this->hasMany(Applicant::class);
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

    public function getEndDateFormattedAttribute(){
        return $this->end_date ? Carbon::parse($this->end_date)->translatedFormat('d F Y') : null;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        // 'phone_number',
        // 'birthplace',
        // 'birthdate',
        // 'address',
        // 'identity_num',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        // 'tgl_lahir' => 'date',
    ];

    // Relasi contoh (jika ada)
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function applicant()
    {
        return $this->hasMany(Applicant::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
}

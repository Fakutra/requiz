<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    //protected $table = 'tb_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone_number',
        'birthplace',
        'birthdate',
        'address',
        'identity_num',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'tgl_lahir' => 'date',
    ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    // Jika 1 user hanya boleh daftar ke 1 posisi
    // public function applicant()
    // {
    //     return $this->hasOne(Applicant::class);
    // }

    // Jika boleh daftar ke banyak posisi
    public function applicant()
    {
        return $this->hasMany(Applicant::class);
    }
}

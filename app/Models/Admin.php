<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    // tambahkan ini kalau kamu pakai "remember_token"
    protected $rememberTokenName = 'remember_token';

    // atau ini kalau ingin semua field bisa diisi
    protected $guarded = [];
}

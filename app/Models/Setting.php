<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'user_id',
        'manual_book_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

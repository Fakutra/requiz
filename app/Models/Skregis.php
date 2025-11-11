<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skregis extends Model
{
    use HasFactory;

    protected $table = 'skregis';

    protected $fillable = [
        'content',
        'title',
        'description',
        // 'order'
    ];
}

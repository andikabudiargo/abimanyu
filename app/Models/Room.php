<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $table = 'rooms';

    protected $fillable = [
        'code',
        'name',
        'capacity',
        'equipment',
        'location',
    ];

    protected $casts = [
        'equipment' => 'array', // biar equipment JSON otomatis jadi array
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Defect extends Model
{
    protected $fillable = [
        'code',
        'defect',
        'description',
        'inspection_post',
        'category',
        'status',
        'raw_material',
    ];

    protected $casts = [
        'raw_material' => 'boolean',
    ];
}


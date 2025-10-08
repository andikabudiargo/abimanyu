<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMaterial extends Model
{
    protected $fillable = ['code', 'name', 'description', 'created_by'];

public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
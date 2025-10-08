<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workstation extends Model
{
    protected $fillable = ['code', 'plant', 'name', 'pic'];

    public function pics()
    {
        return $this->belongsTo(User::class, 'pic');
    }

}
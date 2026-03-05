<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversionValue extends Model
{
    protected $fillable = ['value','effective_date','created_by'];

   public function createdBy()
{
    return $this->belongsTo(User::class, 'created_by', 'id');
}

}


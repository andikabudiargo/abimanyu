<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversion extends Model
{
    protected $fillable = ['conversion_number',
    'year',
    'month',
    'status',
    'total_qty',
    'total_conversion',
    'estimated_profit',
    'created_by',
    'note',];

   public function createdBy()
{
    return $this->belongsTo(User::class, 'created_by', 'id');
}

}


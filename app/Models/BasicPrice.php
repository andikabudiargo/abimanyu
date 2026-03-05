<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BasicPrice extends Model
{

 protected $table = 'basic_prices';
    protected $fillable = ['article_code','purchase_price','selling_price', 'rm_conversion', 'fg_conversion','matome','conversion_value_used','last_calculated_at'];

   public function createdBy()
{
    return $this->belongsTo(User::class, 'created_by', 'id');
}

}


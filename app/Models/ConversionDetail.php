<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversionDetail extends Model
{
    protected $fillable = ['conversion_id',
    'article_code',
    'delivery_qty',
    'material_price',
    'service_Price',
    'total_price',
    'conversion_value',
    'fixed_conversion',
    'conversion',
    'grand_total',];

}


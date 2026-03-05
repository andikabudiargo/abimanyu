<?php

namespace App\Models;

use App\Observers\SjTemporaryObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(SjTemporaryObserver::class)]
class SjTemporary extends Model
{
    protected $table = 'sj_temporary';

    protected $fillable = [
        'delivery_date',
        'customer',
        'article_code',
        'article_desc',
        'delivery_qty',
        'price',
        'service_price',
        'grand_total',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'delivery_qty'  => 'decimal:4',
        'price'         => 'decimal:2',
        'service_price' => 'decimal:2',
        'grand_total'   => 'decimal:2',
    ];
}
<?php

namespace App\Models;

use App\Observers\LpbTemporaryObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(LpbTemporaryObserver::class)]
class LpbTemporary extends Model
{
    protected $table = 'lpb_temporary';

    protected $fillable = [
        'do_date',
        'supplier_code',
        'supplier_name',
        'article_code',
        'article_name',
        'qty',
        'uom',
        'price',
        'total_tanpa_ppn',
    ];

    protected $casts = [
        'do_date'       => 'date',
        'qty'           => 'decimal:4',
        'price'         => 'decimal:2',
        'total_tanpa_ppn' => 'decimal:2',
    ];
}
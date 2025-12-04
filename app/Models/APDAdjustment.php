<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class APDAdjustment extends Model
{
    use HasFactory;

    protected $table = 'apd_adjustments';
    protected $fillable = [
        'transaction_code',
        'reference_number',
        'adjustment_date',
        'adjustment_type',
        'adjustment_reason',
    ];

    // Relasi ke detail item
    public function items()
    {
        return $this->hasMany(APDAdjustmentItem::class, 'adjustment_id');
    }

    
}

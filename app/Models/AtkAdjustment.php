<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class AtkAdjustment extends Model
{
    protected $table = 'atk_adjustments';

    protected $fillable = [
        'type',
        'reason',
        'created_by',
    ];

    public function items()
    {
        return $this->hasMany(AtkAdjustmentItem::class, 'atk_adjustment_id');
    }

}
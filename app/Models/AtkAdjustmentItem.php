<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class AtkAdjustmentItem extends Model
{
    protected $table = 'atk_adjustment_items';

    protected $fillable = [
        'atk_adjustment_id',
        'atk_id',
        'qty',
    ];

     // Relasi ke distribusi induk
    public function adjustment()
    {
        return $this->belongsTo(AtkAdjustment::class, 'atk_adjustment_id');
    }

    // Relasi ke data APD
    public function atk()
    {
        return $this->belongsTo(Atk::class, 'atk_id');
    }

}
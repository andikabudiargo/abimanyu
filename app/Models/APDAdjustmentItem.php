<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class APDAdjustmentItem extends Model
{
    use HasFactory;

    protected $table = 'apd_adjustment_items';
    protected $fillable = [
        'adjustment_id',
        'apd_id',
        'qty',
    ];

    // Relasi ke header
    public function adjustment()
    {
        return $this->belongsTo(APDAdjustment::class, 'adjustment_id');
    }

    // Relasi ke data APD
    public function apd()
    {
        return $this->belongsTo(Apd::class, 'apd_id');
    }
}

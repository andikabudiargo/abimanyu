<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apd extends Model
{
    protected $fillable = [
        'code',
        'name',
        'uom',
        'min_stock',
        'lifetime',
         'conditions',
          'initial_stock',
          'icon'
    ];

   
    public function adjustments()
    {
        return $this->hasMany(ApdAdjustment::class, 'apd_id');
    }

      public function adjustmentItems()
    {
        return $this->hasMany(APDAdjustmentItem::class, 'apd_id');
    }

}

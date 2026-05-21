<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoReferenceMaster extends Model
{
    protected $table = 'sto_reference_masters';

    public function items()
    {
        return $this->hasMany(StoReferenceItem::class, 'sto_reference_id');
    }
}
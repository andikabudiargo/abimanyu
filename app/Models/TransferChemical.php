<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransferChemical extends Model
{
    protected $fillable = [
        'transfer_date',
        'location_from',
        'location_to',
        'created_by',
        'note',
    ];

    protected $casts = [
        'transfer_date' => 'date',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(TransferChemicalItem::class);
    }

    public function createdBy(){
        return $this->belongsTo(User::class,'created_by', 'id');
    }
}
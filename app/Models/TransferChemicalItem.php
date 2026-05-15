<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferChemicalItem extends Model
{
    protected $fillable = [
        'transfer_chemical_id',
        'article_code',
        'condition',
        'qty',
        'unit',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
    ];

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(TransferChemical::class, 'transfer_chemical_id');
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class,'article_code','article_code');
    }
}
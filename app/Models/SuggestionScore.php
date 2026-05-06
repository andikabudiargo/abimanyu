<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuggestionScore extends Model
{
    protected $fillable = [
        'suggestion_id',
        'formula_item_id',
        'score',
    ];

    public function suggestion()
    {
        return $this->belongsTo(Suggestion::class);
    }

    public function formulaItem()
    {
        return $this->belongsTo(
            SuggestionRewardFormulaItem::class,
            'formula_item_id'
        );
    }
}
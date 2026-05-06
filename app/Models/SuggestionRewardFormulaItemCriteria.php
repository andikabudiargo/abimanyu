<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuggestionRewardFormulaItemCriteria extends Model
{
    use HasFactory;

    protected $table = 'suggestion_reward_formula_item_criteria';

    protected $fillable = [
        'item_id',
        'grade',
        'min_point',
        'max_point',
        'description',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function item()
    {
        return $this->belongsTo(
            SuggestionRewardFormulaItem::class,
            'item_id'
        );
    }
}
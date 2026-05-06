<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuggestionRewardFormulaItem extends Model
{
    use HasFactory;

    protected $table = 'suggestion_reward_formula_items';

    protected $fillable = [
        'formula_id',
        'item_name',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function formula()
    {
        return $this->belongsTo(
            SuggestionRewardFormula::class,
            'formula_id',
            'id'
        );
    }

    public function criteria()
    {
        return $this->hasMany(
            SuggestionRewardFormulaItemCriteria::class,
            'item_id',
            'id'
        )->orderBy('id');
    }
}
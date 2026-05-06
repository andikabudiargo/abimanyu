<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuggestionRewardTier extends Model
{
    use HasFactory;

    protected $table = 'suggestion_reward_tiers';

    protected $fillable = [
        'formula_id',
        'min_score',
        'max_score',
        'reward_amount',
        'sort_order',
    ];

    protected $casts = [
        'min_score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'reward_amount' => 'decimal:2',
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
            'formula_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function matchScore(float $score): bool
    {
        return $score >= $this->min_score
            && $score <= $this->max_score;
    }
}
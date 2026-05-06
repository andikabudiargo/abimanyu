<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuggestionRewardFormula extends Model
{
    use HasFactory;

    protected $table = 'suggestion_reward_formulas';

    protected $fillable = [
        'name',
        'is_active',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // User pembuat formula
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Item penilaian (Innovation, Safety, Cost Saving, dll)
     public function items()
    {
        return $this->hasMany(
            SuggestionRewardFormulaItem::class,
            'formula_id', // foreign key
            'id'          // local key
        )->orderBy('sort_order');
    }

    public function tiers()
    {
        return $this->hasMany(
            SuggestionRewardTier::class,
            'formula_id',
            'id'
        )->orderBy('sort_order');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
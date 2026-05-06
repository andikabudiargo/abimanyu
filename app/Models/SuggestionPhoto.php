<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuggestionPhoto extends Model
{
    use HasFactory;

    protected $table = 'suggestion_photos';

    protected $fillable = [
        'suggestion_id',
        'type',
        'file_path',
        'file_name',
        'file_size',
        'sort_order',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Satu foto dimiliki oleh satu suggestion
     */
    public function suggestion()
    {
        return $this->belongsTo(Suggestion::class, 'suggestion_id');
    }
}
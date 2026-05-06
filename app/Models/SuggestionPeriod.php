<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuggestionPeriod extends Model
{
    use HasFactory;

    protected $table = 'suggestion_periods';

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
        'max_submissions',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'start_date'      => 'date',
        'end_date'        => 'date',
        'is_active'       => 'boolean',
        'max_submissions' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // User pembuat periode
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Semua suggestion yang masuk pada periode ini
    public function suggestions()
    {
        return $this->hasMany(Suggestion::class, 'period_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    // Ambil hanya periode aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    // Cek apakah periode masih berjalan
    public function isRunning(): bool
    {
        $today = now()->toDateString();

        return $today >= $this->start_date->toDateString()
            && $today <= $this->end_date->toDateString();
    }
}
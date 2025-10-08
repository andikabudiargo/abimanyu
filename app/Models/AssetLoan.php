<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetLoan extends Model
{
    use HasFactory;

    protected $table = 'assets_loans';

    protected $fillable = [
        'asset_id',
        'user_id',
        'purpose',
        'date_loan',
        'return_estimation',
        'date_return',
        'status',
        'condition_return',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejected_reason',
        'condition_return',
        'condition_note',
        'photo_after_return'
    ];

    // Relations
    public function asset()
    {
        return $this->belongsTo(ITAsset::class, 'asset_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejector()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
}

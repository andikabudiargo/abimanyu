<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AtkRequest extends Model
{
    protected $table = 'atk_requests';

    protected $fillable = [
        'request_number',
        'department',
        'status',
        'note',
        'created_by',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'rejected_reason',
        'distributed_at',
        'distributed_by',
        'received_at',
        'received_by',
    ];

    protected $casts = [
        'approved_at'    => 'datetime',
        'rejected_at'    => 'datetime',
        'distributed_at' => 'datetime',
        'received_at'    => 'datetime',
    ];

    // ─── Relations ───────────────────────────────────────────

    public function items(): HasMany
    {
        return $this->hasMany(AtkRequestItem::class, 'atk_request_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'rejected_by');
    }

    public function distributedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'distributed_by');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'received_by');
    }

    
    public function departemen(){
        return $this->belongsTo(Department::class, 'department', 'id');
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'submitted'          => 'bg-blue-50 text-blue-600 border-blue-200',
            'approved'           => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'rejected'           => 'bg-red-50 text-red-600 border-red-200',
            'distributed'        => 'bg-purple-50 text-purple-600 border-purple-200',
            'received'           => 'bg-teal-50 text-teal-700 border-teal-200',
            'returned_from_spv'  => 'bg-amber-50 text-amber-700 border-amber-200',
            'returned_from_mr'   => 'bg-orange-50 text-orange-700 border-orange-200',
            default              => 'bg-gray-50 text-gray-500 border-gray-200',
        };
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DocumentRegistration extends Model
{
    protected $table = 'document_registrations';

    protected $fillable = [
        'registration_number',
        'department_id',
        'document_number',
        'document_type',
        'submission_type',
        'document_title',
        'reason',
        'need_4m',
        'file_path',
        'file_4m_path',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'authorized_by',
        'authorized_at',
        'rejected_by',
        'rejected_at',
        'rejected_reason',
    ];

    protected $casts = [
        'need_4m'       => 'boolean',
        'approved_at'   => 'datetime',
        'returned_at'   => 'datetime',
        'authorized_at' => 'datetime',
        'rejected_at'   => 'datetime',
        'returned_reason' => 'array',
    ];

    public function currentDocument()
{
    return $this->hasOne(Document::class, 'document_number', 'document_number');
}

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function authorizedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function returnedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    public function revision(): HasOne
    {
        return $this->hasOne(DocumentRevision::class, 'registration_id');
    }

    public function isRevisionOrObsolete(): bool
    {
        return in_array($this->submission_type, ['Revision', 'Obsolete']);
    }

    public function copies()
    {
        return $this->hasMany(DocumentCopy::class, 'registration_id');
    }

    public function notes()
{
    return $this->hasMany(DocumentNote::class, 'registration_id');
}
}
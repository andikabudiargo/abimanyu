<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'documents';

    protected $fillable = [
        'registration_id',
        'document_number',
        'document_type',
        'document_title',
        'remark',
        'current_version',
        'file_path',
        'file_4m_path',
        'is_active',
        'dept_from',
        'dept_to',
        'submitted_by',
        'submitted_at',
        'published_by',
        'published_at',
    ];

    // ✅ karena ada updated_at di tabel
    public $timestamps = true;

    const CREATED_AT = null; // ❌ tidak ada created_at
    const UPDATED_AT = 'updated_at';

    // =========================
    // RELATIONS
    // =========================

    public function registration()
    {
        return $this->belongsTo(DocumentRegistration::class, 'registration_id');
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    // =========================
    // SCOPES (BIAR ENAK DIPAKAI)
    // =========================

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeByNumber($query, $number)
    {
        return $query->where('document_number', $number);
    }

}
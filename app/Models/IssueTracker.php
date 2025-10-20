<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueTracker extends Model
{
    use HasFactory;

    protected $table = 'issue_trackers'; // ubah sesuai nama tabel kamu

    protected $fillable = [
        'request_number',
        'location_area',
        'request_type',
        'description',
        'attachment',
        'urgency',
        'recommendation',
        'recommended_action',
        'duration_work',
        'check_result',
        'status',

        // Tracking & Approval
        'created_by',
        'created_at',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejected_reason',
        'checked_by',
        'checked_at',
        'verification_by',
        'verification_at',
        'authorized_by',
        'authorized_at',
        'assigned_by',
        'assigned_at',
        'done_by',
        'done_at',
        'closed_by',
        'closed_at',

        // Progress & Evidence
        'work_start',
        'work_end',
        'evidence_before',
        'evidence_after',
        'work_verification',
        'confirmation',
        'rating',
        'feedback',
        'updated_at',
    ];

    public $timestamps = false; // karena kamu sudah punya created_at & updated_at manual

    /* ===============================
     * RELATIONSHIPS
     * =============================== */

    // User yang membuat permintaan
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // User yang menyetujui
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // User yang memeriksa
    public function checker()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    // User yang memverifikasi
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verification_by');
    }

    // User yang mengotorisasi
    public function authorizer()
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }

    // User yang menyelesaikan
    public function finisher()
    {
        return $this->belongsTo(User::class, 'done_by');
    }

    // User yang menutup tiket
    public function closer()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

     public function materials()
    {
        return $this->hasMany(IssueTrackerMaterial::class, 'issue_tracker_id');
    }
}

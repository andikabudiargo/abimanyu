<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User;

class Suggestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'ss_number', 'categories', 'theme', 'department',
        'discovery_date', 'location', 'background',
        'root_cause', 'improvement_activity', 'evaluation_result',
        'standardization', 'status', 'completion_step',
        'reviewed_by_spv', 'reviewed_at_spv', 'spv_note',
        'scored_by_manager', 'scored_at',
        'score_total', 'reward_amount', 'manager_note', 'period_id',
    ];

    protected $casts = [
        'categories'       => 'array',
        'discovery_date'   => 'date',
        'reviewed_at_spv'  => 'datetime',
        'scored_at'        => 'datetime',
        'score_total'      => 'decimal:2',
        'reward_amount'    => 'decimal:2',
    ];

    // ── Relasi ──
    public function user()        { return $this->belongsTo(User::class, 'user_id'); }
    public function spvReviewer() { return $this->belongsTo(User::class, 'reviewed_by_spv'); }
    public function managerScorer(){ return $this->belongsTo(User::class, 'scored_by_manager'); }
    public function photos()      { return $this->hasMany(SuggestionPhoto::class); }
    public function photosBefore(){ return $this->hasMany(SuggestionPhoto::class)->where('type','before'); }
    public function photosAfter() { return $this->hasMany(SuggestionPhoto::class)->where('type','after'); }
    public function period()      { return $this->belongsTo(SuggestionPeriod::class); }

    // ── Status helpers ──
    const STATUS_DRAFT        = 'draft';
    const STATUS_SUBMITTED    = 'submitted';
    const STATUS_APPROVED_SPV = 'approved_spv';
    const STATUS_REJECTED_SPV = 'rejected_spv';
    const STATUS_RETURNED_SPV = 'returned_spv';
    const STATUS_SCORED       = 'scored';
    const STATUS_CLOSED       = 'closed';

    public function creator()
{
    return $this->belongsTo(User::class, 'created_by');
}

public function departments()
{
    return $this->belongsTo(Department::class, 'department');
}

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft'        => 'Draft',
            'submitted'    => 'Menunggu Review SPV',
            'approved_spv' => 'Disetujui SPV',
            'rejected_spv' => 'Ditolak SPV',
            'returned_spv' => 'Dikembalikan SPV',
            'scored'       => 'Sudah Dinilai',
            'closed'       => 'Selesai',
            default        => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft'        => 'gray',
            'submitted'    => 'blue',
            'approved_spv' => 'teal',
            'rejected_spv' => 'red',
            'returned_spv' => 'amber',
            'scored'       => 'purple',
            'closed'       => 'green',
            default        => 'gray',
        };
    }

    public function getCompletionPercentAttribute(): int
    {
        $steps = [
            1 => 20,  // Identitas + background
            2 => 40,  // Root cause (minimum submit)
            3 => 60,  // Aktivitas perbaikan
            4 => 80,  // Foto before/after
            5 => 100, // Evaluasi + standarisasi
        ];
        return $steps[$this->completion_step] ?? 0;
    }

    // ── Auto-generate SS Number ──
    public static function generateSsNumber(): string
    {
        $year  = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        return sprintf('SS-%s-%04d', $year, $count);
    }

    // ── Scopes ──
    public function scopeForDepartment($query, string $dept)
    {
        return $query->where('department', $dept);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scores()
{
    return $this->hasMany(SuggestionScore::class);
}

public function getAvailableActions(User $user): array
{
    $actions = [];

    $isOwner   = $this->user_id == $user->id;
    $isSpv     = $user->roles()->where('name', 'Supervisor Special Access')->exists();
    $isManager = $user->roles()->where('name', 'Manager Special Access')->exists();

    /*
    |----------------------------------------------------------
    | OWNER
    |----------------------------------------------------------
    */
    if ($isOwner && in_array($this->status, ['draft', 'returned_spv'])) {
        $actions[] = 'submit';
    }

    /*
    |----------------------------------------------------------
    | SPV
    |----------------------------------------------------------
    */
    if ($isSpv && $this->status === 'submitted' && !$isOwner) {
        $actions[] = 'approve';
        $actions[] = 'reject';
        $actions[] = 'return';
    }

    /*
    |----------------------------------------------------------
    | MANAGER
    |----------------------------------------------------------
    */
    if ($isManager && $this->status === 'approved_spv') {
        $actions[] = 'score';
    }

    /*
    |----------------------------------------------------------
    | AFTER SCORE
    |----------------------------------------------------------
    */
    if ($this->status === 'scored') {
        $actions[] = 'close';
    }

    return $actions;
}
}
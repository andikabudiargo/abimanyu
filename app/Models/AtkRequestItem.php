<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AtkRequestItem extends Model
{
    protected $table = 'atk_request_items';

    protected $fillable = [
        'atk_request_id',
        'atk_id',
        'qty',
    ];

    protected $casts = [
        'qty' => 'integer',
    ];

    // ─── Relations ───────────────────────────────────────────

    public function request(): BelongsTo
    {
        return $this->belongsTo(AtkRequest::class, 'atk_request_id');
    }

    public function atk(): BelongsTo
    {
        return $this->belongsTo(Atk::class, 'atk_id');
    }
}
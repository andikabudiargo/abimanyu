<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sto extends Model
{
    use HasFactory;

    protected $table = 'stos';

    protected $fillable = [
        'sto_number',
        'warehouse',
        'note',
        'created_by',
    ];

    /* =====================
     | RELATIONSHIPS
     ===================== */

    // STO → banyak item
    public function items()
    {
        return $this->hasMany(StoItem::class, 'sto_id');
    }

    // STO → user pembuat
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

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
        'area',
        'shelves',
        'note',
        'created_by',
        'created_by_2',
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

// Optional: hapus otomatis sto_items saat sto dihapus
protected static function booted()
{
    static::deleting(function ($sto) {
        $sto->items()->delete();
    });
}

}

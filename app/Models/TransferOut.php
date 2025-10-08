<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferOut extends Model
{
    use HasFactory;

    protected $table = 'transfer_out';

    protected $fillable = [
        'code',
        'reference_number',
        'date',
        'transfer_type',
        'note',
        'created_by',
    ];

    // ✅ Relasi ke TransferOutItem
    public function items()
    {
        return $this->hasMany(TransferOutItem::class, 'transfer_out_id');
    }

    public function createdBy()
{
    return $this->belongsTo(User::class, 'created_by');
}
}

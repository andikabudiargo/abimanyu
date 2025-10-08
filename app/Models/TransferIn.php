<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferIn extends Model
{
     protected $table = 'transfer_in'; // nama tabel

    protected $fillable = [
        'code',
        'date',
        'reference_number',
        'transfer_category',
        'supplier_id',
        'from_location',
        'note',
        'qr_code_path',
        'created_by',
    ];

    public function fromLocation()
{
    return $this->belongsTo(Warehouse::class, 'from_location');
}

public function supplier()
{
    return $this->belongsTo(Supplier::class, 'supplier_code', 'code');
}

public function warehouse()
{
    return $this->belongsTo(Warehouse::class, 'from_location', 'id');
}

public function toLocation()
{
    return $this->belongsTo(Warehouse::class, 'to_location');
}

public function items()
{
    return $this->hasMany(TransferInItems::class, 'transfer_in_id', 'id');
}

public function createdBy()
{
    return $this->belongsTo(User::class, 'created_by', 'id');
}



    public $timestamps = true; // kalau pakai created_at, updated_at
}

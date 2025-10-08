<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'order_number',
        'order_date',
        'delivery_date',
        'supplier_code',
        'top',
        'pkp',
        'note',
        'subtotal',
        'discount',
        'use_ppn',
        'use_pph',
        'ppn',
        'pph',
        'netto',
        'created_by', // <-- ini wajib
    ];

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

   public function requests()
{
    return $this->belongsToMany(PurchaseRequest::class, 'purchase_order_request');
}

public function createdBy()
{
    return $this->belongsTo(User::class, 'created_by');
}

public function approved()
{
    return $this->belongsTo(User::class, 'approved_by');
}

public function authorized()
{
    return $this->belongsTo(User::class, 'authorized_by');
}

public function verified()
{
    return $this->belongsTo(User::class, 'verified_by');
}

// PurchaseOrder.php
public function supplier()
{
    return $this->belongsTo(Supplier::class, 'supplier_code', 'code');
}
// Model PurchaseOrder.php
public function pr()
{
    return $this->belongsTo(PurchaseRequest::class, 'purchase_request_id'); // misal kolom foreign key 'pr_id'
}




}


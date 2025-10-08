<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Department; // <- Tambahkan ini
use App\Models\User;       // <- Dan ini juga

class PurchaseRequest extends Model
{
      protected $fillable = [
        'request_number',
        'request_date',
        'order_type',
        'stock_needed_at',
        'sales_order_id',
        'ga_request_id',
        'pr_note',
        'created_by'
    ];

    public function items()
    {
        return $this->hasMany(PurchaseRequestItem::class);
    }

  public function owner()
{
    return $this->belongsTo(User::class, 'created_by');
}

public function approve()
{
    return $this->belongsTo(User::class, 'approved_by');
}

public function reject()
{
    return $this->belongsTo(User::class, 'rejected_by');
}

public function authorized()
{
    return $this->belongsTo(User::class, 'authorized_by');
}

public function verified()
{
    return $this->belongsTo(User::class, 'verified_by');
}

// App\Models\PurchaseRequest.php
public function supplier()
{
    return $this->belongsTo(Supplier::class);
}

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
{
    return $this->belongsToMany(PurchaseOrder::class, 'purchase_order_request');
}

public function purchaseOrders()
{
    return $this->hasMany(PurchaseOrder::class, 'purchase_request_id');
}


}

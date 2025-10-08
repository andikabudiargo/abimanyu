<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receiving extends Model
{
    protected $table = 'receivings';

    protected $fillable = [
        'purchase_order_id',
        'receiving_number',
        'received_date',
        'supplier_code',
        'delivery_order_number',
        'delivery_order_date',
        'account_payable_id',
        'revision',
        'revision_reason',
        'note',
        'created_by',
        'checked_at',
        'checked_by',
        'verified_at',
        'verified_by',
    ];

    protected $dates = [
        'received_date',
        'do_date',
        'created_at',
        'updated_at',
        'checked_at',
        'verified_at',
    ];

    public function items()
    {
        return $this->hasMany(ReceivingItem::class, 'receiving_id');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_code', 'code');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function checker()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequestItem extends Model
{
    public $timestamps = false;

      protected $fillable = [
        'purchase_request_id',
        'article_code',
        'qty',
        'qty_po', // tambahkan ini
        'uom',
        'note'
    ];

    public function request()
{
    return $this->belongsTo(PurchaseRequest::class, 'purchase_request_id');
}

    public function article()
{
    return $this->belongsTo(Article::class, 'article_code', 'article_code'); // Sesuaikan foreign key-nya
}

public function poItems()
{
    return $this->hasMany(PurchaseOrderItem::class, 'purchase_request_id');
}

public function purchaseOrderItem()
{
    return $this->hasOne(PurchaseOrderItem::class, 'purchase_request_id');
}

}

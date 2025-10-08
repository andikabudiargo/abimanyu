<?php

namespace App\Models;
use App\Models\Article;       // <- Dan ini juga

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'purchase_request_id',
        'article_code',
        'qty',
        'price',
        'total',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function request()
    {
        return $this->belongsTo(PurchaseRequestItem::class, 'purchase_request_id', 'id');
    }

    public function article()
{
    return $this->belongsTo(Article::class, 'article_code', 'article_code'); // Sesuaikan foreign key-nya
}
}


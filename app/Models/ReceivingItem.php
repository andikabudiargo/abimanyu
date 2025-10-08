<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceivingItem extends Model
{
    protected $table = 'receiving_items';

    protected $fillable = [
        'receiving_id',
        'po_item_id', // ← tambahkan ini
        'article_code',
        'qty_po',
        'qty_received',
        'qty_free',
        'qty_total',
        'destination_id',
        'expired_date',
    ];

    // Relasi ke Receiving
    public function receiving()
    {
        return $this->belongsTo(Receiving::class);
    }

    // Jika ingin relasi ke artikel
    public function article()
    {
        return $this->belongsTo(Article::class, 'article_code', 'article_code');
    }

    public function purchaseOrderItem()
{
    return $this->belongsTo(PurchaseOrderItem::class, 'po_item_id');
}

 public function destination()
    {
        return $this->belongsTo(Warehouse::class, 'destination_id');
    }


}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferInItems extends Model
{
      protected $table = 'transfer_in_items'; // nama tabel

    protected $fillable = [
        'id',
        'transfer_in_id',
        'destination_id',
        'code',
        'article_code',
        'description',
        'qty',
        'qty_return',
        'expired_date',
        'origin_item_id',
        'origin_type',
        'created_at',
    ];

    public function destination()
{
    return $this->belongsTo(Warehouse::class, 'destination_id');
}
 public function article()
    {
        return $this->belongsTo(Article::class, 'article_code', 'article_code');
    }
 public function transferIn()
    {
        return $this->belongsTo(TransferIn::class, 'transfer_in_id', 'id');
    }

}

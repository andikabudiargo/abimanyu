<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferOutItem extends Model
{
    use HasFactory;

    protected $table = 'transfer_out_items';

    protected $fillable = [
        'transfer_out_id',
        'transfer_in_code',
        'transfer_in_item_id',
        'article_code',
        'description',
        'qty',
        'uom',
        'min_package',
        'expired_date',
        'from_location',
        'destination',
    ];

    // ✅ Relasi ke TransferOut
    public function transferOut()
    {
        return $this->belongsTo(TransferOut::class, 'transfer_out_id');
    }

    // ✅ (Opsional) Relasi ke TransferInItem jika ingin akses item asal
    public function transferInItem()
    {
        return $this->belongsTo(TransferInItems::class, 'transfer_in_item_id');
    }

    public function fromLocation()
{
    return $this->belongsTo(Warehouse::class, 'from_location_id');
}

public function destination()
{
    return $this->belongsTo(Warehouse::class, 'destination_id');
}
 public function article()
    {
        return $this->belongsTo(Article::class, 'article_code', 'article_code');
    }

}

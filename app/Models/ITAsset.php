<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ITAsset extends Model
{
    protected $table = 'it_assets'; // 👈 wajib biar ke tabel it_assets

    protected $fillable = [
        'asset_number','asset_name','asset_type','acquistion_type','supplier_id',
        'purchase_date','warranty','assignment_type','assigned_to',
        'location','conditions','photo', 'status', 'note'
    ];

    public function user()
{
    return $this->belongsTo(User::class, 'assigned_to');
}

public function supplier()
{
    return $this->belongsTo(Supplier::class, 'supplier_id'); 
}

}

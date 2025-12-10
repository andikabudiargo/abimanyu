<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityGoodItem extends Model
{
    use HasFactory;

    protected $table = 'security_good_items';
    protected $fillable = [
        'security_good_id',
        'nama_barang',
        'jumlah',
        'foto',
        'kondisi',
        'catatan',
    ];

    // Relasi ke header
    public function barang()
    {
        return $this->belongsTo(SecurityGood::class, 'security_good_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityGood extends Model
{
    use HasFactory;

    protected $table = 'security_goods';
    protected $fillable = [
        'jenis_barang',
         'perusahaan',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'nama_pengirim',
        'identitas',
        'nomor_kendaraan',
        'nama_penerima',
        'surat_jalan',
    ];

    // Relasi ke detail item
    public function items()
    {
        return $this->hasMany(SecurityGoodItem::class, 'security_good_id');
    }

    
}

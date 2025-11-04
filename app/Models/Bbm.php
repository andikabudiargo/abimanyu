<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bbm extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_bbm',
        'harga_bbm',
    ];

    /**
     * Relasi ke Armada
     * Satu BBM bisa digunakan oleh banyak armada.
     */
    public function armadas()
    {
        return $this->hasMany(Armada::class, 'bbm_id');
    }

    /**
     * Format harga BBM secara otomatis.
     */
    public function getHargaBbmFormattedAttribute()
    {
        return 'Rp ' . number_format($this->harga_bbm, 0, ',', '.');
    }
}

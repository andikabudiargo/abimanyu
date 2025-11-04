<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Armada extends Model
{
    use HasFactory;

    protected $table = 'armada';

    protected $fillable = [
        'nama_armada',
        'bbm_id',
        'rasio',
        'spare',
        'icon',
    ];

    /**
     * Relasi ke tabel BBM
     * Satu armada hanya menggunakan satu jenis BBM.
     */
    public function bbm()
    {
        return $this->belongsTo(Bbm::class, 'bbm_id');
    }

    /**
     * Accessor: format spare menjadi rupiah.
     */
    public function getSpareFormattedAttribute()
    {
        return 'Rp ' . number_format($this->spare, 0, ',', '.');
    }

    /**
     * Accessor: format rasio agar tampil rapi (contoh: 1:8.5)
     */
    public function getRasioFormattedAttribute()
    {
        return $this->rasio ? '1:' . $this->rasio : '-';
    }
}

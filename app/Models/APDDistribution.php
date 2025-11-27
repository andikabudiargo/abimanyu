<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class APDDistribution extends Model
{
    use HasFactory;

    // Nama tabel (jika tidak sama dengan nama model jamak)
    protected $table = 'apd_distributions';

    // Primary key
    protected $primaryKey = 'id';

    // Kolom yang bisa diisi (mass assignable)
    protected $fillable = [
        'distribution_number',
        'distribution_date',
        'note',
        'created_by',
    ];

    // Aktifkan timestamps (karena kamu punya created_at dan updated_at)
    public $timestamps = true;

    // Format tanggal otomatis sebagai Carbon instance
    protected $dates = [
        'distribution_date',
        'created_at',
        'updated_at',
    ];

    /**
     * Relasi ke model User (yang membuat distribusi)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke item distribusi
    public function items()
    {
        return $this->hasMany(APDDistributionItem::class, 'apd_distribution_id');
    }
}

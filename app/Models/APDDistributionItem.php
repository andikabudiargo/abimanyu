<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class APDDistributionItem extends Model
{
    use HasFactory;

    protected $table = 'apd_distribution_items';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'apd_distribution_id',
        'apd_id',
        'qty',
        'receiver',
        'created_at',
        'updated_at',
    ];

    protected $dates = ['created_at', 'updated_at'];

    // Relasi ke distribusi induk
    public function distribution()
    {
        return $this->belongsTo(APDDistribution::class, 'apd_distribution_id');
    }

    // Relasi ke data APD
    public function apd()
    {
        return $this->belongsTo(Apd::class, 'apd_id');
    }

    // Relasi ke penerima (user)
    public function receiverUser()
    {
        return $this->belongsTo(Employee::class, 'receiver');
    }
}

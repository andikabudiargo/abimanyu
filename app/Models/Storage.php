<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{
    protected $fillable = [
        'storage_type',
        'category',
        'name',
        'capacity',
        'used',
    ];

    // Accessor untuk free capacity
    public function getFreeAttribute()
    {
        return $this->capacity - $this->used;
    }

    // Accessor untuk persentase penggunaan
    public function getUsedPercentAttribute()
    {
        return $this->capacity > 0 ? round(($this->used / $this->capacity) * 100, 2) : 0;
    }

    // Accessor untuk status warning
    public function getStatusAttribute()
    {
        return $this->used_percent >= $this->warning_limit ? 'WARNING' : 'OK';
    }
}

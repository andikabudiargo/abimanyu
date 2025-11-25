<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FingerLog extends Model
{
    protected $table = 'finger_logs';

    protected $primaryKey = 'id';

    public $timestamps = true;

    // Kolom yang boleh diisi
    protected $fillable = [
        'machine_id',
        'nik',
        'status',
        'timestamp',
        'raw_data',
        'log_hash',
    ];

    // Agar raw_data otomatis di-cast sebagai array
    protected $casts = [
        'raw_data' => 'array',
        'timestamp' => 'datetime',
    ];

    /**
     * Scope untuk filter berdasarkan NIK
     */
    public function scopeNik($query, $nik)
    {
        if ($nik) {
            return $query->where('nik', $nik);
        }
        return $query;
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeDate($query, $date)
    {
        if ($date) {
            return $query->whereDate('timestamp', $date);
        }
        return $query;
    }

    /**
     * Scope untuk filter berdasarkan rentang waktu
     */
    public function scopeBetween($query, $start, $end)
    {
        if ($start && $end) {
            return $query->whereBetween('timestamp', [$start, $end]);
        }
        return $query;
    }

    /**
     * Membuat log_hash otomatis (opsional)
     * hash = md5(machine_id + nik + timestamp)
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->log_hash) {
                $model->log_hash = md5(
                    $model->machine_id . '|' .
                    $model->nik . '|' .
                    $model->timestamp
                );
            }
        });
    }
}

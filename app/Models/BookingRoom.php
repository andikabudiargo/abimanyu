<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingRoom extends Model
{
    use HasFactory;

    protected $table = 'booking_rooms';

    protected $fillable = [
        'booking_date',
        'start_time',
        'end_time',
        'purpose',
        'description',
        'status',
        'room_id',
        'created_by',
        'cancel_by',
        'cancel_at',
        'approved_by',
        'approved_at',
        'cancel_reason',
    ];

    /**
     * Relasi ke Room
     */
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

     public function cancel()
    {
        return $this->belongsTo(User::class, 'cancel_by');
    }

    public function approved()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}

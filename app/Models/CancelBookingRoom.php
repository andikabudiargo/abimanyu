<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CancelBookingRoom extends Model
{
    use HasFactory;

    protected $table = 'cancel_booking_rooms';

    protected $fillable = [
        'booking_id',
        'user_id',
        'room_id',
        'booking_date',
        'start_time',
        'end_time',
        'purpose',
        'reason',
    ];

    // Relasi ke BookingRoom
    public function booking()
    {
        return $this->belongsTo(BookingRoom::class, 'booking_id');
    }

     public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    // Relasi ke User (yang melakukan cancel, misalnya Admin GA)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

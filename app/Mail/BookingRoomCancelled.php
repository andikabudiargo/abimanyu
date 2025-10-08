<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\BookingRoom;

class BookingRoomCancelled extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct(BookingRoom $booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        return $this->subject('Your Room Booking Has Been Cancelled')
                    ->view('emails.booking_room_cancelled');
    }
}

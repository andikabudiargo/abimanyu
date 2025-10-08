<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\BookingRoom; // gunakan model BookingRoom

class BookingRoomRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $booking; // ubah nama properti supaya lebih jelas

    // Constructor menerima BookingRoom
    public function __construct(BookingRoom $booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        return $this->subject('New Room Booking Request Needs Approval')
                    ->view('emails.booking_room_request'); // sesuaikan view jika perlu
    }
}

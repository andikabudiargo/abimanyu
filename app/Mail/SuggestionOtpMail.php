<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SuggestionOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public string $userName;

    public function __construct(string $otp, string $userName)
    {
        $this->otp      = $otp;
        $this->userName = $userName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Suggestion System] Kode Verifikasi Anda — ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'auth.otp-email',
            with: [
                'otp'      => $this->otp,
                'userName' => $this->userName,
                'expiry'   => 5, // menit
            ]
        );
    }
}
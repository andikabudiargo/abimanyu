<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class CapaReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $action;
    public $dayStatus;

    public function __construct($action)
    {
        $this->action = $action;

        $today = Carbon::today();
        $due = Carbon::parse($action->due_date);

        if ($due->equalTo($today->copy()->addDays(3))) {
            $this->dayStatus = 'H-3 Reminder';
        } elseif ($due->equalTo($today)) {
            $this->dayStatus = 'Due Today';
        } elseif ($due->equalTo($today->copy()->subDays(3))) {
            $this->dayStatus = 'H+3 Overdue Reminder';
        } else {
            $this->dayStatus = 'Reminder';
        }
    }

    public function build()
    {
        return $this->subject('CAPA Due Date Reminder - ' . $this->dayStatus)
                    ->view('emails.capa_reminder_formal');
    }
}
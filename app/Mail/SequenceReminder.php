<?php

namespace App\Mail;

use App\SequenceReminder as Reminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SequenceReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $reminder;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Reminder $reminder)
    {
        $this->reminder = $reminder;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {   
        if($this->reminder->is_enabled) {
            return $this->from(config('mail.from.address'), getsetting('from_email_name'))
                        ->subject($this->reminder->subject)
                        ->html($this->reminder->html);
        }
    }
}

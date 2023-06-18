<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExportCompleted extends Mailable
{
    use Queueable, SerializesModels;

    public $export;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($export)
    {
        $this->export = $export;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.from.address'), getsetting('from_email_name'))
                    ->subject('Export Completed')
                    ->markdown('emails.exports.completed');
    }
}

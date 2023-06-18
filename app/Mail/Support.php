<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Support extends Mailable
{
    use Queueable, SerializesModels;

    public $attributes;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($attributes = null)
    {
        $this->attributes = $attributes;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if(!empty($this->attributes['email']))
            $this->replyTo($this->attributes['email']);

        return $this->subject('New Support Request')->view('emails.support');
    }
}

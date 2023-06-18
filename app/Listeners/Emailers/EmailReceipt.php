<?php

namespace App\Listeners\Emailers;

use Mail;
use App\EmailNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class EmailReceipt
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if(EmailNotification::where('name', 'Payment Confirmation')->first()->is_enabled)
            Mail::to($event->user->email)->send(new \App\Mail\Receipt($event->user, $event->receipt));
    }
}

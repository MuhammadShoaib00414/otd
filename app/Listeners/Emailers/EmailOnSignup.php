<?php

namespace App\Listeners\Emailers;

use Mail;
use App\EmailNotification;
use Illuminate\Auth\Events\Registered;

class EmailOnSignup
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
     * @param  \App\Events\Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
	try {
        	if($emailNotification = EmailNotification::find(1)->is_enabled)
            		Mail::to($event->user->email)->send(new \App\Mail\Signup($event->user));
	} catch (\Exception $e) {

	}
    }
}

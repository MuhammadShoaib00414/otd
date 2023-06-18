<?php

namespace App\Listeners\Loggers;

use App\Events\UserSignedIn;

class LogSignin
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
     * @param  \App\Events\OrderShipped  $event
     * @return void
     */
    public function handle(UserSignedIn $event)
    {
        $event->user->logs()->create([
            'action'  => 'signin'
        ]);
    }
}
<?php

namespace App\Listeners\Loggers;

use Illuminate\Auth\Events\Registered;

class LogSignup
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
        $event->user->logs()->create([
            'action'  => 'signup',
            'message' => 'Register through a registered link',
            'track_info' => UserTrackInfo()
        ]);
    }
}
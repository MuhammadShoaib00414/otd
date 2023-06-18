<?php

namespace App\Listeners\Loggers;

use App\Events\ProfileUpdated;

class LogProfileUpdate
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
    public function handle(ProfileUpdated $event)
    {
        $event->user->logs()->create([
            'action' => 'update profile'
        ]);
    }
}
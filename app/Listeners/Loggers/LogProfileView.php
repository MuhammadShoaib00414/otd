<?php

namespace App\Listeners\Loggers;

use App\Events\ViewProfile;

class LogProfileView
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
    public function handle(ViewProfile $event)
    {
        $event->user->logs()->create([
            'action'             => 'view profile',
            'related_model_type' => get_class($event->profile),
            'related_model_id'   => $event->profile->id,
        ]);
    }
}
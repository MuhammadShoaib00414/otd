<?php

namespace App\Listeners\Loggers;

use App\Events\EventCreated;

class LogEventCreated
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
    public function handle(EventCreated $event)
    {
        $event->user->logs()->create([
            'action'             => 'create event',
            'related_model_type' => get_class($event->calendarEvent),
            'related_model_id'   => $event->calendarEvent->id,
        ]);
    }
}
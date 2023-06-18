<?php

namespace App\Listeners\Loggers;

use App\Events\RsvpChanged;

class LogRsvpChanged
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
    public function handle(RsvpChanged $event)
    {
        $event->user->logs()->create([
            'action'             => 'rsvp to event',
            'related_model_type' => get_class($event->rsvp->event),
            'related_model_id'   => $event->rsvp->event->id,
            'message'        => 'responded: ' . $event->rsvp->response,
        ]);
    }
}
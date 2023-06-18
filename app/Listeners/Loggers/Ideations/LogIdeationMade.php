<?php

namespace App\Listeners\Loggers\Ideations;

use App\Events\Ideations\NewIdeation;

class LogIdeationMade
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
    public function handle(NewIdeation $event)
    {
        $event->user->logs()->create([
            'action'             => 'new ideation',
            'related_model_type' => get_class($event->ideation),
            'related_model_id'   => $event->ideation->id,
        ]);
    }
}
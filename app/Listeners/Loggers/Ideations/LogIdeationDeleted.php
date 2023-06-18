<?php

namespace App\Listeners\Loggers\Ideations;

use App\Events\Ideations\IdeationDeleted;

class LogIdeationDeleted
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
    public function handle(IdeationDeleted $event)
    {
        $event->user->logs()->create([
            'action'             => 'deleted ideation',
            'related_model_type' => get_class($event->ideation),
            'related_model_id'   => $event->ideation->id,
        ]);

        $event->ideation->notifications()->delete();
    }
}
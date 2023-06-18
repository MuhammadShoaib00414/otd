<?php

namespace App\Listeners\Loggers\Ideations;

use App\Events\Ideations\IdeationReplied;

class LogIdeationReplied
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
    public function handle(IdeationReplied $event)
    {
        $event->user->logs()->create([
            'action'             => 'ideation reply',
            'related_model_type' => get_class($event->ideation),
            'related_model_id'   => $event->ideation->id,
        ]);
    }
}
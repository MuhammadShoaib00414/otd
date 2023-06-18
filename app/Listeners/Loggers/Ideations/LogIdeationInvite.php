<?php

namespace App\Listeners\Loggers\Ideations;

use App\Events\Ideations\IdeationInvite;

class LogIdeationInvite
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
    public function handle(IdeationInvite $event)
    {
        $event->user->logs()->create([
            'action'             => 'ideation invite',
            'related_model_type' => get_class($event->ideation),
            'related_model_id'   => $event->ideation->id,
            'message' => $event->description,
        ]);
    }
}
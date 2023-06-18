<?php

namespace App\Listeners\Loggers;

use App\Events\ShoutoutMade;

class LogShoutout
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
    public function handle(ShoutoutMade $event)
    {
        if($event->user)
        {
            $event->user->logs()->create([
                'action'             => 'shoutout',
                'related_model_type' => get_class($event->post->post),
                'related_model_id'   => $event->post->post->id,
            ]);
        }
    }
}
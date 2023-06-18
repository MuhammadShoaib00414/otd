<?php

namespace App\Listeners\Loggers;

use App\Events\NewPost;

class LogPostMade
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
    public function handle(NewPost $event)
    {
        $event->user->logs()->create([
            'action'             => 'new post',
            'related_model_type' => get_class($event->post),
            'related_model_id'   => $event->post->id,
        ]);
    }
}
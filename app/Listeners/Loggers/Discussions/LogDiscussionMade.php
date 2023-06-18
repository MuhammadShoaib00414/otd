<?php

namespace App\Listeners\Loggers\Discussions;

use App\Events\Discussions\NewDiscussion;

class LogDiscussionMade
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
    public function handle(NewDiscussion $event)
    {
        $event->user->logs()->create([
            'action'             => 'made discussion',
            'related_model_type' => get_class($event->discussion),
            'related_model_id'   => $event->discussion->id,
            'message' => '<a href="/groups/' . $event->group->slug . '">' . 'group: ' . $event->group->name . '</a>',
        ]);
    }
}

<?php

namespace App\Listeners\Loggers\Discussions;

use App\Events\Discussions\DiscussionDeleted;

class LogDiscussionDeleted
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
    public function handle(DiscussionDeleted $event)
    {
        $event->user->logs()->create([
            'action'             => 'deleted discussion',
            'related_model_type' => get_class($event->discussion),
            'related_model_id'   => $event->discussion->id,
            'message' => '<a href="/groups/' . $event->group->slug . '">' . 'group: ' . $event->group->name . '</a>',
        ]);
    }
}
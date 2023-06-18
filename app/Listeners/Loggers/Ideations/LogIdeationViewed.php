<?php

namespace App\Listeners\Loggers\Ideations;

use App\Events\Ideations\IdeationViewed;

class LogIdeationViewed
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
    public function handle(IdeationViewed $event)
    {
        $event->user->logs()->create([
            'action'             => 'viewed ideation',
            'related_model_type' => get_class($event->ideation),
            'related_model_id'   => $event->ideation->id,
        ]);
        $event->ideation->notifications()->where('user_id', $event->user->id)->update(['viewed_at' => \Carbon\Carbon::now()]);
    }
}
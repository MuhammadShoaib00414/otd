<?php

namespace App\Listeners\Loggers;

use App\Events\IntroductionMade;

class LogIntroduction
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
    public function handle(IntroductionMade $event)
    {
        $event->user->logs()->create([
            'action'             => 'introduction',
            'related_model_type' => get_class($event->introduction),
            'related_model_id'   => $event->introduction->id,
        ]);
    }
}
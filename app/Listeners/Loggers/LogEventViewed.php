<?php

namespace App\Listeners\Loggers;

use Carbon\Carbon;
use App\Events\EventViewed;

class LogEventViewed
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
    public function handle(EventViewed $event)
    {
        $timesEventViewedInPast5Minutes = $event->user->logs()->where('action', '=', 'view event')
                                                              ->where('related_model_id', '=', $event->calendarEvent->id)
                                                              ->where('created_at', '>=', Carbon::now()->subMinutes(5)->toDateTimeString())->count();

        if ($timesEventViewedInPast5Minutes == 0) {
            $event->user->logs()->create([
                'action'             => 'view event',
                'related_model_type' => get_class($event->calendarEvent),
                'related_model_id'   => $event->calendarEvent->id,
            ]);
        }
    }
}
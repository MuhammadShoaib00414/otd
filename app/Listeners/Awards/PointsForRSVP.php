<?php

namespace App\Listeners\Awards;

use App\Point;
use App\AwardedPoint;
use App\Events\RsvpChanged;

class PointsForRSVP
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
     * @param  \App\Events\IntroductionMade  $event
     * @return void
     */
    public function handle(RsvpChanged $event)
    {
        $timesRSVPdToThisEvent = $event->user->logs()->where('action', '=', 'rsvp to event')
                                                     ->where('related_model_id', '=', $event->rsvp->event_id)
                                                     ->count();

        if ($timesRSVPdToThisEvent == 1)
            $event->user->awardPoint('rsvp-event');
    }
}
<?php

namespace App\Listeners\Awards;

use App\Point;
use App\AwardedPoint;
use App\Events\ShoutoutMade;

class PointsForShoutout
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
    public function handle(ShoutoutMade $event)
    {
        if($event->user)
        {
            $event->user->awardPoint('make-shoutout');
            $event->post->post->shouted->awardPoint('receive-shoutout');
        }
    }
}
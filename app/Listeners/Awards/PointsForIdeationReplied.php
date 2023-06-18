<?php

namespace App\Listeners\Awards;

use App\Point;
use App\AwardedPoint;
use App\Events\Ideations\IdeationReplied;

class PointsForIdeationReplied
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
    public function handle(IdeationReplied $event)
    {
        $event->user->awardPoint('ideation-reply');
    }
}
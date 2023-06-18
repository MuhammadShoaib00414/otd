<?php

namespace App\Listeners\Awards;

use App\Point;
use App\AwardedPoint;
use App\Events\Ideations\IdeationViewed;

class PointsForIdeationViewed
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
    public function handle(IdeationViewed $event)
    {
        $event->user->awardPoint('view-ideation');
    }
}
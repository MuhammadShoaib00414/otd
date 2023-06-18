<?php

namespace App\Listeners\Awards;

use App\Point;
use App\AwardedPoint;
use App\Events\Ideations\IdeationInvite;

class PointsForIdeationInvite
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
    public function handle(IdeationInvite $event)
    {
        $event->user->awardPoint('ideation-invite');
    }
}
<?php

namespace App\Listeners\Awards;

use App\Point;
use App\AwardedPoint;
use App\Events\Discussions\DiscussionReplied;

class PointsForDiscussionReply
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
    public function handle(DiscussionReplied $event)
    {
        $event->user->awardPoint('discussion-reply');
    }
}
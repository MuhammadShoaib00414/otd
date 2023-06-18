<?php

namespace App\Listeners\Awards;

use App\Point;
use App\AwardedPoint;
use Illuminate\Auth\Events\Registered;

class PointsForSignup
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
    public function handle(Registered $event)
    {
        $event->user->awardPoint('join');
    }
}
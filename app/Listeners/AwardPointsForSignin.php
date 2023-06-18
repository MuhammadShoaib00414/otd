<?php

namespace App\Listeners;

use App\Point;
use Carbon\Carbon;
use App\AwardedPoint;
use App\Events\UserSignedIn;

class AwardPointsForSignin
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
    public function handle(UserSignedIn $event)
    {
        $loginTimesThisWeek = $event->user->logs()->where('action', '=', 'signin')
                                                  ->where('created_at', '>=', Carbon::now()->startOfWeek()->toDateTimeString())->count();

        if ($loginTimesThisWeek == 1)
            $event->user->awardPoint('weekly-signon-1x');
        else if ($loginTimesThisWeek == 5)
            $event->user->awardPoint('weekly-signon-5x');
    }
}
<?php

namespace App\Listeners\Awards;

use App\Point;
use App\AwardedPoint;
use App\Events\Budgets\ExpenseSaved;

class PointsForExpenseSaved
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
    public function handle(ExpenseSaved $event)
    {
        $event->user->awardPoint('add-expense');
    }
}
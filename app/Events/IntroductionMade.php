<?php

namespace App\Events;

use App\User;
use App\Introduction;
use Illuminate\Queue\SerializesModels;

class IntroductionMade
{
    use SerializesModels;

    public $user;
    public $introduction;

    /**
     * Create a new event instance.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function __construct(User $user, Introduction $introduction)
    {
        $this->user = $user;
        $this->introduction = $introduction;
    }
}
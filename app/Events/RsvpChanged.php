<?php

namespace App\Events;

use App\User;
use App\EventRsvp;
use Illuminate\Queue\SerializesModels;

class RsvpChanged
{
    use SerializesModels;

    public $user;
    public $rsvp;

    /**
     * Create a new event instance.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function __construct(User $user, EventRsvp $rsvp)
    {
        $this->user = $user;
        $this->rsvp = $rsvp;
    }
}
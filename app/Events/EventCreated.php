<?php

namespace App\Events;

use App\User;
use App\Event;
use Illuminate\Queue\SerializesModels;

class EventCreated
{
    use SerializesModels;

    public $user;
    public $calendarEvent;

    /**
     * Create a new event instance.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function __construct(User $user, Event $calendarEvent)
    {
        $this->user = $user;
        $this->calendarEvent = $calendarEvent;
    }
}
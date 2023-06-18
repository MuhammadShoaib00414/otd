<?php

namespace App\Events;

use App\User;
use App\Event;
use Illuminate\Queue\SerializesModels;

class EventViewed
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

        $this->checkNotifications();
    }

    public function checkNotifications()
    {
        $this->calendarEvent->notifications()->where('user_id', $this->user->id)->update(['viewed_at' => \Carbon\Carbon::now()]);
    }
}
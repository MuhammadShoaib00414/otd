<?php

namespace App\Listeners\Notifications;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class EventCancelled implements ShouldQueue
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
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $users = $event->event->allow_rsvps ? $event->event->attending()->distinct()->get() : $event->event->group->users;

        $this->createNotifications($users, $event->event);
    }

    public function createNotifications($users, $event)
    {
        foreach($users as $user)
        {
            $notification = $event->notifications()->create([
                'user_id' => $user->id,
                'action' => 'Event Cancelled',
            ]);
            $notification->send();
        }
    }
}

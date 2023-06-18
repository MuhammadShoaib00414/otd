<?php

namespace App\Listeners\Notifications;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LeftWaitlist implements ShouldQueue
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
        $user = $event->user;
        $event = $event->event;

        $this->createNotification($user, $event);
    }

    public function createNotification($user, $event)
    {
        $notification = $event->notifications()->create([
            'user_id' => $user->id,
            'action' => 'Left Waitlist',
        ]);
        $notification->send();
    }
}

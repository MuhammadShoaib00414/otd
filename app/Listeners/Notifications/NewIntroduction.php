<?php

namespace App\Listeners\Notifications;

use Carbon\Carbon;
use App\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NewIntroduction implements ShouldQueue
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
        $users = $event->introduction->users;

        $this->createNotifications($users, $event->introduction);
    }

    public function createNotifications($users, $introduction)
    {
        foreach($users as $user)
        {
            $notification = $introduction->notifications()->create([
                'user_id' => $user->id,
                'action' => 'New Introduction',
            ]);
            $notification->send();
        }
    }
}

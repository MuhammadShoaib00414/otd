<?php

namespace App\Listeners\Notifications;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NewDiscussion implements ShouldQueue
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
        $users = $event->group->users;

        $this->createNotifications($users, $event->discussion);
    }

    public function createNotifications($users, $discussion)
    {
        foreach($users as $user)
        {
            $notification = $discussion->notifications()->create([
                'user_id' => $user->id,
                'action' => 'New Discussion',
            ]);
            $notification->send();
        }
    }
}

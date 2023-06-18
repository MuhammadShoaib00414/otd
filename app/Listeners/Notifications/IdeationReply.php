<?php

namespace App\Listeners\Notifications;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class IdeationReply implements ShouldQueue
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
        $users = $event->ideation->participants()->where('user_id', '<>', $event->user->id)->get();

        $this->createNotifications($users, $event->ideation);
    }

    public function createNotifications($users, $ideation)
    {
        foreach($users as $user)
        {
            $notification = $ideation->notifications()->create([
                'user_id' => $user->id,
                'action' => 'Ideation Reply',
            ]);
            $notification->send();
        }
    }
}

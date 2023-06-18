<?php

namespace App\Listeners\Notifications;

use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class IdeationInvite implements ShouldQueue
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
        $userIds = $event->users;

        $this->createNotifiations($userIds, $event->ideation);
    }

    public function createNotifiations($userIds, $ideation)
    {
        foreach($userIds as $userId)
        {
            $notification = $ideation->notifications()->create([
                'user_id' => $userId,
                'action' => 'Ideation Invitation',
            ]);
            $notification->send();
        }
    }
}

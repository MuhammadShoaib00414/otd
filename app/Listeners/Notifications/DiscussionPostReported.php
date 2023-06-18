<?php

namespace App\Listeners\Notifications;

use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DiscussionPostReported implements ShouldQueue
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
        $users = $event->group->admins->merge(User::admins()->get());

        $this->createNotifications($users, $event->discussion, $event->user->id);
    }

    public function createNotifications($users, $discussion, $reported_by)
    {
        foreach($users as $user)
        {
            $notification = $discussion->notifications()->create([
                'user_id' => $user->id,
                'action' => 'Discussion Post Reported',
                'notes' => ['reported_by' => $reported_by],
            ]);

            $notification->send();
        }
    }
}

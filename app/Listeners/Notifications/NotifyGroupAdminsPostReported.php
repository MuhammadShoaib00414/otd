<?php

namespace App\Listeners\Notifications;

use App\Events\PostReported;
use App\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyGroupAdminsPostReported implements ShouldQueue
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
    public function handle(PostReported $event)
    {
        if($event->group instanceof \App\Group)
        {
            foreach($event->group->admins()->get() as $admin) {
                Notification::create([
                    'notifiable_type' => 'App\Post',
                    'notifiable_id' => $event->postId,
                    'user_id' => $admin->id,
                    'action' => 'Post Reported',
                    'email_notification_id' => 7,
                ]);
            }
        }
    }
}

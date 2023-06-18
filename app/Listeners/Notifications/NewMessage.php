<?php

namespace App\Listeners\Notifications;

use Carbon\Carbon;
use App\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Bus\Queueable;

class NewMessage implements ShouldQueue
{
    use Queueable, InteractsWithQueue;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        \Log::info('New Message.');
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
        $users = $event->thread->participants()->where('users.id', '!=', $event->user->id)->get();
        \Log::info('New Message in handle.', ['users' => $users ]);
        $this->createNotifications($users, $event);
    }

    private function createNotifications($users, $event)
    {
        \Log::info('New Message in create Notifications function.', ['users' => $users ]);
        foreach($users as $user)
        {
            $notification = Notification::create([
                'notifiable_type' => get_class($event->thread),
                'notifiable_id' => $event->thread->id,
                'user_id' => $user->id,
                'action' => 'New Message',
            ]);
        \Log::info('New Message .', ['notification' => $notification ]);

            $notification->send();
        }
    }
}

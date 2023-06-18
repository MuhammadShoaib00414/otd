<?php

namespace App\Listeners\Notifications;

use Carbon\Carbon;
use App\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NewEvent implements ShouldQueue
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
        $groups = collect([$event->calendarEvent->group])->merge($event->calendarEvent->groups);
        $users = $groups->pluck('users')->flatten()->unique('id')->where('id', '!=', $event->calendarEvent->created_by);

        $this->createNotifications($users, $event->calendarEvent);
    }

    public function createNotifications($users, $calendarEvent)
    {
        foreach($users as $user)
        {
            $notification = Notification::create([
                'notifiable_type' => get_class($calendarEvent),
                'notifiable_id' => $calendarEvent->id,
                'user_id' => $user->id,
                'action' => 'New Event',
            ]);

            $notification->send();
        }
    }
}

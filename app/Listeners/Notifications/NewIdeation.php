<?php

namespace App\Listeners\Notifications;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NewIdeation implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(\App\Events\Ideations\NewIdeation $event)
    {
        $this->user = $event->user;
        $this->ideation = $event->ideation;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        foreach($event->ideation->invited_users as $user)
        {
            $notification = $event->ideation->notifications()->create([
                'user_id' => $user->id,
                'action' => 'New Ideation',
            ]);
            $when = $user->should_sms ? Carbon::now() : Carbon::now()->addMinutes(10);
            $notification->send($when);
        }
    }
}

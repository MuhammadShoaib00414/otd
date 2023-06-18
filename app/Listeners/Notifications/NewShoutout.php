<?php

namespace App\Listeners\Notifications;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NewShoutout implements ShouldQueue
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
        $shoutout = $event->post->post;

        $this->createNotification($shoutout->shouted, $shoutout);
    }

    public function createNotification($user, $shoutout)
    {
        $notification = $shoutout->notifications()->create([
            'user_id' => $user->id,
            'action' => 'New Shoutout',
        ]);
        $notification->send();
    }
}

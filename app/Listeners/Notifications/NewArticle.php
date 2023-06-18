<?php

namespace App\Listeners\Notifications;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NewArticle implements ShouldQueue
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
        $users = $event->post->listing->groups->pluck('users')->flatten()->where('id', '!=', $event->user->id);

        $this->createNotifications($users, $event->post);
    }

    public function createNotifications($users, $post)
    {
        foreach($users as $user)
        {
            $notification = $post->notifications()->create([
                'user_id' => $user->id,
                'action' => 'New Article Post',
            ]);
            $notification->send();
        }
    }
}

<?php

namespace App\Listeners\Notifications;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewPost implements ShouldQueue
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
        Log::info('before'. $event->post->listing->groups);
        $users = $event->post->listing->group->users->flatten()->where('id', '!=', $event->user->id);
        Log::info('after run query '. $users);

        $this->createNotifications($users, $event->post);
    }

    public function createNotifications($users, $post)
    {
        foreach($users as $user)
        {
            $notification = $post->notifications()->create([
                'user_id' => $user->id,
                'action' => 'New Post',
            ]);
            $notification->send();
        }
    }
}

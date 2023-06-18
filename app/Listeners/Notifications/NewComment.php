<?php

namespace App\Listeners\Notifications;


use App\Post;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use App\Notification as NotificationModel;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\NewCommentEvent as NewCommentEvent;

class NewComment
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
      
    }

    /**
     * Handle the event.
     *
     * @param  NewComment  $event
     * @return void
     */
   
    public function handle($event)
    {
        $this->createNotifications($event->post);
    }

    public function createNotifications($post)
    {
        $notification = $post->post->user->notifications()->create([
            'notifiable_type' => 'App\Post',
            'notifiable_id' =>  $post->id,
            'action' => 'Comment on Post',
            'sent_at' => \Carbon\Carbon::now(),
            'notes' => [
                'commented_by' => \Auth::user()->id
            ]
        ]);
        $notification->send();
    }
}

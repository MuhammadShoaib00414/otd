<?php

namespace App\Listeners\Notifications;

use App\User;
use App\Post;
use Carbon\Carbon;
use App\Events\PostReported as ReportedPostEvent;
use App\ReportedPost;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PostReported implements ShouldQueue
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
    public function handle(ReportedPostEvent $event)
    {
        $post = Post::find($event->postId);
        $reportedBy = ReportedPost::where('postable_id', $event->postId)->first()->reported_by;
        if(isset($event->group) && $event->group)
            $users = $event->group->admins->merge(User::admins()->get());
        elseif(isset($post->group))
            $users = $post->group->admins->merge(User::admins()->get());
        else
            $users = User::admins()->get();

        $this->createNotifications($users, $post, $event->group, $reportedBy);
    }

    public function createNotifications($users, $post, $group, $reportedBy)
    {
        foreach($users as $user)
        {
            $notification = $post->notifications()->create([
                'user_id' => $user->id,
                'action' => 'Post Reported',
                'notes' => ['reported_by' => $reportedBy]
            ]);

            $notification->send(false, $group);
        }
    }
}

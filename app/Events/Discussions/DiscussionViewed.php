<?php

namespace App\Events\Discussions;

use App\User;
use App\DiscussionThread;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DiscussionViewed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, DiscussionThread $thread)
    {
        $this->user = $user;
        $this->thread = $thread;

        $this->checkNotifications();
    }

    public function checkNotifications()
    {
        $this->thread->notificationsFor($this->user->id)->update(['viewed_at' => \Carbon\Carbon::now()]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}

<?php

namespace App\Events\Discussions;

use App\User;
use App\DiscussionThread;
use App\Group;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DiscussionPostReported
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $discussion;
    public $group;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, DiscussionThread $discussion, Group $group)
    {
        $this->group = $group;
        $this->discussion = $discussion;
        $this->user = $user;
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

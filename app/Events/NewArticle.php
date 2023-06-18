<?php

namespace App\Events;

use App\User;
use App\ArticlePost;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewArticle
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $post;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, ArticlePost $post)
    {
        $this->post = $post;
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

<?php

namespace App\Events;

use App\User;
use App\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PostReported
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $group;
    public $postId;
    public $userName;
    public $isArticle;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($group, $postId, $userName, $isArticle = false)
    {
        $this->group = $group;
        $this->postId = $postId;
        $this->reported_by_name = $userName;
        $this->is_article = $isArticle;
    }
}

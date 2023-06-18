<?php

namespace App\Events;

use App\Post;
use App\User;
use Illuminate\Queue\SerializesModels;

class ShoutoutMade
{
    use SerializesModels;

    public $user;
    public $post;

    /**
     * Create a new event instance.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function __construct($user, Post $post)
    {
        $this->user = $user;
        $this->post = $post;
    }
}
<?php

namespace App\Events;

use App\User;
use App\TextPost;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;

class NewPost
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
    public function __construct(User $user, TextPost $post)
    {
        $this->user = $user;
        $this->post = $post;
    }
}
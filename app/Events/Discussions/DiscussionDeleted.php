<?php

namespace App\Events\Discussions;

use App\User;
use App\Group;
use App\DiscussionThread;
use Illuminate\Queue\SerializesModels;

class DiscussionDeleted
{
    use SerializesModels;

    public $user;
    public $discussion;
    public $group;

    /**
     * Create a new event instance.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function __construct(User $user, DiscussionThread $discussion, Group $group)
    {
        $this->user = $user;
        $this->discussion = $discussion;
        $this->group = $group;

        $discussion->notifications()->delete();
    }
}
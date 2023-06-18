<?php

namespace App\Events\Ideations;

use App\User;
use App\Ideation;
use Illuminate\Queue\SerializesModels;

class IdeationInvite
{
    use SerializesModels;

    public $users;
    public $user;
    public $ideation;
    public $description;

    /**
     * Create a new event instance.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function __construct(User $user, Ideation $ideation, int $numUsersInvited, $userIds)
    {
        $this->users = $userIds;
        $this->user = $user;
        $this->ideation = $ideation;
    }
}
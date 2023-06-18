<?php

namespace App\Events\Ideations;

use App\User;
use App\Ideation;
use Illuminate\Queue\SerializesModels;

class IdeationDeleted
{
    use SerializesModels;

    public $user;
    public $ideation;

    /**
     * Create a new event instance.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function __construct(User $user, Ideation $ideation)
    {
        $this->user = $user;
        $this->ideation = $ideation;
    }
}
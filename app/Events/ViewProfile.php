<?php

namespace App\Events;

use App\User;
use Illuminate\Queue\SerializesModels;

class ViewProfile
{
    use SerializesModels;

    public $user;
    public $profile;

    /**
     * Create a new event instance.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function __construct(User $user, $profile)
    {
        $this->user = $user;
        $this->profile = $profile;
    }
}
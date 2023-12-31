<?php

namespace App\Events;

use App\User;
use Illuminate\Queue\SerializesModels;

class ProfilePhotoUploaded
{
    use SerializesModels;

    public $user;

    /**
     * Create a new event instance.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
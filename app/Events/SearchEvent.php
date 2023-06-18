<?php

namespace App\Events;

use App\User;
use Illuminate\Queue\SerializesModels;

class SearchEvent
{
    use SerializesModels;

    public $user;
    public $searchTerm;

    /**
     * Create a new event instance.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function __construct(User $user, $searchTerm)
    {
        $this->user = $user;
        $this->searchTerm = $searchTerm;
    }
}
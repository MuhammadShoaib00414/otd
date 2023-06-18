<?php

namespace App\Events;

use App\Ideation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IdeationProposed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ideation;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Ideation $ideation)
    {
        $this->ideation = $ideation;
    }

}

<?php

namespace App\Events;

use App\User;
use App\Message;
use App\MessageThread;
use Illuminate\Queue\SerializesModels;

class MessageSent
{
    use SerializesModels;

    public $user;
    public $message;
    public $thread;

    /**
     * Create a new event instance.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function __construct(User $user, MessageThread $thread, Message $message)
    { 
         \Log::info('Message Sent.', ['users' => $user]);
        $this->user = $user;
        $this->thread = $thread;
        $this->message = $message;
    }
}
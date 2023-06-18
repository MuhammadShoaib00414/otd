<?php

namespace App\Listeners\Loggers;

use App\Events\MessageSent;

class LogMessageSent
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\OrderShipped  $event
     * @return void
     */
    public function handle(MessageSent $event)
    {
        $event->user->logs()->create([
            'action'             => 'message',
            'related_model_type' => get_class($event->message),
            'related_model_id'   => $event->message->id,
        ]);
    }
}
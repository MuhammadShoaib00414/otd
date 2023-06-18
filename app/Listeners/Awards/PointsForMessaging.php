<?php

namespace App\Listeners\Awards;

use App\Point;
use App\AwardedPoint;
use App\Events\MessageSent;

class PointsForMessaging
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
        $thread = $event->message->thread;
        $messages = $thread->messages;

        $messagesUserSent = $thread->messages->where('sending_user_id', $event->user->id)->count();
        $messagesRecipientSent = $thread->messages->whereNotIn('sending_user_id', [$event->user->id])->count();

        if ($messagesRecipientSent > 0 && $messagesUserSent == 1) {
            $thread->participants->map(function ($user) {
                $user->awardPoint('conversation');
            });
        }
    }
}
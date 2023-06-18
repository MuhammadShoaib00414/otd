<?php

namespace App\Listeners\Emailers;

use Mail;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Log;

class EmailOnMessage
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
     * @param  \App\Events\Registered  $event
     * @return void
     */
    public function handle(MessageSent $event)
    {
        foreach($event->thread->participants as $user) {
            if ($user->id != $event->user->id && $user->notification_frequency == 'immediately' && $user->notification_method == 'email' && \App\EmailNotification::find(2)->is_enabled) {
                $this->checkNotifications($user, $event);
                Mail::to($user->email)->send(new \App\Mail\NewMessage($user));
            }
            else if($user->notification_frequency == 'immediately' && $user->notification_method == 'sms' && $user->phone && getsetting('is_message_sms_notifications_enabled')) {
                event(new \App\Events\SmsNotification($user));
            }
        }
    }

    public function checkNotifications($user, $event)
    {
        $event->thread->notifications()->whereNull('sent_at')->whereNull('viewed_at')->where('user_id', $user->id)->update(['sent_at' => \Carbon\Carbon::now()]);
    }
}
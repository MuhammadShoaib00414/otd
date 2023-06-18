<?php

namespace App\Listeners\Emailers;

use Mail;
use App\Events\ShoutoutMade;

class EmailOnShoutout
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
    public function handle(ShoutoutMade $event)
    {
        $user = $event->post->post->shouted;
        if($user->notification_frequency == 'immediately' && $user->notification_method == 'email' && \App\EmailNotification::find(4)->is_enabled)
        {
            $this->checkNotifications($event, $user);
            Mail::to($user->email)->send(new \App\Mail\NewShoutout($user, $event->post));
        }
        else if($user->notification_frequency == 'immediately' && $user->notification_method == 'sms' && $user->phone && getsetting('is_shoutout_sms_notifications_enabled'))
        {
            event(new \App\Events\SmsNotification($user));
        }
    }

    public function checkNotifications($event, $user)
    {
        $event->post->post->notifications()->whereNull('sent_at')->whereNull('viewed_at')->where('user_id', $user->id)->update(['sent_at' => \Carbon\Carbon::now()]);
    }
}
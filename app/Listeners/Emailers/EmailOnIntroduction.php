<?php

namespace App\Listeners\Emailers;

use Mail;
use App\Events\IntroductionMade;

class EmailOnIntroduction
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
    public function handle(IntroductionMade $event)
    {
        foreach($event->introduction->users as $user)
        {
            if($user->notification_frequency == 'immediately' && $user->notification_method == 'email' && \App\EmailNotification::find(3)->is_enabled)
            {
                $this->checkNotifications($user, $event);
                Mail::to($user->email)->send(new \App\Mail\NewIntroduction($user));
            }
            else if($user->notification_frequency == 'immediately' && $user->notification_method == 'sms' && $user->phone && getsetting('is_introduction_sms_notifications_enabled'))
            {
                event(new \App\Events\SmsNotification($user));
            }
        }
    }

    public function checkNotifications($user, $event)
    {
        $event->introduction->notifications()->whereNull('sent_at')->whereNull('viewed_at')->where('user_id', $user->id)->update(['sent_at' => \Carbon\Carbon::now()]);
    }
}
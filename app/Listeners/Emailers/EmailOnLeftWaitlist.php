<?php

namespace App\Listeners\Emailers;

use Mail;
use App\Events\LeftWaitlist;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailOnLeftWaitlist
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
     * @param  object  $event
     * @return void
     */
    public function handle(LeftWaitlist $event)
    {
        if($event->user->notification_frequency == 'immediately' && $event->user->notification_method == 'email' && \App\EmailNotification::find(5)->is_enabled)
        {
            $this->checkNotifications($event->user, $event);
            Mail::to($event->user->email)->send(new \App\Mail\LeftWaitlist($event->user, $event->event));
        }
        else if($event->user->notification_frequency == 'immediately' && $event->user->notification_method == 'sms' && $event->user->phone && getsetting('is_event_sms_notifications_enabled'))
        {
            event(new \App\Events\SmsNotification($event->user));
        }
    }

    public function checkNotifications($user, $event)
    {
        $event->event->notifications()->whereNull('sent_at')->whereNull('viewed_at')->where('user_id', $user->id)->update(['sent_at' => \Carbon\Carbon::now()]);
    }
}

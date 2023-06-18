<?php

namespace App\Listeners\Emailers;

use Mail;
use App\Events\EventCancelled;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailOnEventCancelled
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
    public function handle(EventCancelled $event)
    {
        foreach($event->users as $user)
        {
            if($user->notification_frequency == 'immediately' && $user->notification_method == 'email' && \App\EmailNotification::find(6)->is_enabled)
            {
                $this->checkNotifications($user, $event);
                Mail::to($user->email)->send(new \App\Mail\EventCancelled($user, $event->event));
            }
            else if($user->notification_frequency == 'immediately' && $user->notification_method == 'sms' && $user->phone && getsetting('is_event_sms_notifications_enabled'))
            {
                event(new \App\Events\SmsNotification($user));
            }
        }
    }

    public function checkNotifications($user, $event)
    {
        $event->event->notifications()->whereNull('sent_at')->whereNull('viewed_at')->where('user_id', $user->id)->update(['sent_at' => \Carbon\Carbon::now()]);
    }
}

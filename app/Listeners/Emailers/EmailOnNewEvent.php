<?php

namespace App\Listeners\Emailers;

use Mail;
use App\EmailNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailOnNewEvent
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
    public function handle($event)
    {
        foreach ($event->calendarEvent->group->users as $user) {
            \Log::info('user deleted_at column: '. $user->deleted_at);
            if ($user->deleted_at != null) {
                \Log::info('Skipping email:  '. $user->email);
                continue;
            }
            \Log::info('sending event notification to '. $user->email);
            if ($user->notification_frequency == 'immediately' && $user->notification_method == 'email' && \App\EmailNotification::find(9)->is_enabled) {
                $this->checkNotifications($user, $event);
                Mail::to($user->email)->send(new \App\Mail\NewEvent($user, $event->calendarEvent));
            } else if ($user->notification_frequency == 'immediately' && $user->notification_method == 'sms' && $user->phone && getsetting('is_event_sms_notifications_enabled')) {
                event(new \App\Events\SmsNotification($user));
            }
        }
    }

    public function checkNotifications($user, $event)
    {
        $event->calendarEvent->notifications()->whereNull('sent_at')->whereNull('viewed_at')->where('user_id', $user->id)->update(['sent_at' => \Carbon\Carbon::now()]);
    }
}

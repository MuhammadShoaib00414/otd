<?php

namespace App\Listeners\Emailers\Ideations;

use Mail;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailOnNewIdeation
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
        $users = User::whereIn('id', $event->ideation->invitations()->distinct('user_id')->pluck('user_id'))->get();

        foreach($users as $user)
        {
            if($user->notification_frequency == 'immediately' && $user->notification_method == 'email' && \App\EmailNotification::find(10)->is_enabled)
            {
                $this->checkNotifications($user, $event);
                Mail::to($user->email)->send(new \App\Mail\NewIdeation($user, $event->ideation));
            }
            else if($user->notification_frequency == 'immediately' && $user->notification_method == 'sms' && $user->phone && getsetting('is_ideation_sms_notifications_enabled'))
            {
                event(new \App\Events\SmsNotification($user));
            }
        }
    }

    public function checkNotifications($user, $event)
    {
        $event->ideation->notifications()->whereNull('sent_at')->whereNull('viewed_at')->where('user_id', $user->id)->where('user_id', $user->id)->update(['sent_at' => \Carbon\Carbon::now()]);
    }
}

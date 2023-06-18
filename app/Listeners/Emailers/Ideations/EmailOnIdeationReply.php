<?php

namespace App\Listeners\Emailers\Ideations;

use Mail;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailOnIdeationReply
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
        $users = collect([$event->ideation->owner])->merge($event->ideation->active_users)->unique();

        foreach($users as $user)
        {
            if($user->id == $event->user->id)
                continue;
            if($user->notification_frequency == 'immediately' && $user->notification_method == 'email' && \App\EmailNotification::find(12)->is_enabled)
            {
                $this->checkNotifications($user, $event);
                Mail::to($user->email)->send(new \App\Mail\DiscussionReply($user, $event->ideation));
            }
            else if($user->notification_frequency == 'immediately' && $user->notification_method == 'sms' && $user->phone && getsetting('is_ideation_sms_notifications_enabled'))
            {
                event(new \App\Events\SmsNotification($user));
            }
        }
    }

    public function checkNotifications($user, $event)
    {
        $event->ideation->notifications()->whereNull('sent_at')->whereNull('viewed_at')->where('user_id', $user->id)->update(['sent_at' => \Carbon\Carbon::now()]);
    }
}

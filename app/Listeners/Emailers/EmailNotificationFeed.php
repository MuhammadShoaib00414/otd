<?php

namespace App\Listeners\Emailers;

use Mail;
use App\EmailNotification;
use Illuminate\Support\Facades\Log;
use App\Mail\NotificationFeed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailNotificationFeed
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
        $emailNotification = EmailNotification::find(14);
        if($emailNotification->is_enabled)
        {
            foreach($event->users as $user)
            {
                Mail::to($user->email)->send(new \App\Mail\NotificationFeed($user));
                $this->checkNotifications($user);
            }
        }
    }

    public function checkNotifications($user)
    {
        $user->notifications()->whereNull('sent_at')->whereNull('viewed_at')->update(['sent_at' => \Carbon\Carbon::now()]);
    }
}

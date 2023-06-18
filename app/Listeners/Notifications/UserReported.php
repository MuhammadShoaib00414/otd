<?php

namespace App\Listeners\Notifications;

use App\Post;
use App\User;
use App\Setting;
use Carbon\Carbon;
use App\Notification as NotificationModel;
use App\EmailNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\UserReported as UserReportedEvent;

class UserReported implements ShouldQueue
{

    /**
     * Create a new notification instance.
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
    public function handle(UserReportedEvent $event)
    {
        $reportedBy = $event->reportedBy;
        $reported = $event->reported;
        $email = Setting::where('name', 'technical_assistance_email')->first()->value;
        $this->createNotifications($email, $reportedBy, $reported);
    }

    public function createNotifications($email, $reportedBy, $reported)
    {
            $notification = NotificationModel::create([
                'notifiable_type' => 'App\User',
                'notifiable_id' => $reported->id,
                'user_id' => $reportedBy->id,
                'action' => 'User Reported',
                'email_notification_id' => EmailNotification::where('name', 'On user reported')->first()->id,
                'sent_at' => \Carbon\Carbon::now(),
            ]);
            $notification->send(false, $email);
    }
}

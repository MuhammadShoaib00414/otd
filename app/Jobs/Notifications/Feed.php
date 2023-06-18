<?php

namespace App\Jobs\Notifications;

use Mail;
use Carbon\Carbon;
use App\EmailNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Feed implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $mostRecentNotificationId;
    public $SMSContent;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $mostRecentNotificationId)
    {
        $this->user = $user;
        $this->mostRecentNotificationId = $mostRecentNotificationId;
        $this->SMSContent = "You have " . $user->unsentNotifications()->count() . " new notifications. " . config('app.url') . "/notifications";
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // this runs 15 minutes (originally) after the notification was created.
        // check if a new consolidatable notification has been created since then.
        // if it has, do nothing. if it hasnt, send the notification.
        $hasHadNewNotificationSinceDispatch = $this->user->notifications()->where('id', '>', $this->mostRecentNotificationId)->whereNotIn('notifiable_type', get_non_consolidatable_notification_types())->where('action', '!=', 'Post Reported')->where('action', '!=', 'Discussion Post Reported')->exists();

        if($hasHadNewNotificationSinceDispatch)
            return;

        if(!$this->user->notifications()->whereNull('viewed_at')->whereNull('sent_at')->exists())
            return;

        $this->sendNotification();
    }

    public function sendNotification()
    {
        if($this->user->immediate_notification_method == 'sms')
        {
            send_sms($this->user->phone, $this->SMSContent);
            $this->updateNotifications();
        }
        elseif($this->user->immediate_notification_method == 'email' && EmailNotification::find(14)->is_enabled)
        {
            Mail::to($this->user->email)->send(new \App\Mail\NotificationFeed($this->user));
            $this->updateNotifications();
        }
    }

    public function updateNotifications()
    {
        $this->user->notifications()->whereNull('sent_at')->update(['sent_at' => Carbon::now()]);
    }
}

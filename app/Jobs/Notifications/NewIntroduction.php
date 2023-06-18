<?php

namespace App\Jobs\Notifications;

use Mail;
use Carbon\Carbon;
use App\Notification;
use App\EmailNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NewIntroduction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $notification;

    public $SMSContent;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;

        $this->SMSContent = getSetting('name') . ": ".$notification->notifiable->invitee->name." introduced you to ".$notification->notifiable->otherUser($notification->user_id)->name.".\n".config('app.url') . '/introductions/' . $notification->notifiable->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->notification->user->immediate_notification_method == 'sms' && getsetting('is_introduction_sms_notifications_enabled'))
        {
            send_sms($this->notification->user->phone, $this->SMSContent);
            $this->updateNotification();
        }
        elseif($this->notification->user->immediate_notification_method == 'email' && EmailNotification::find(3)->is_enabled)
        {
            Mail::to($this->notification->user->email)->send(new \App\Mail\NewIntroduction($this->notification->user));
            $this->updateNotification();
        }
    }

    public function updateNotification()
    {
        $this->notification->update([
            'sent_at' => Carbon::now(),
        ]);
    }
}

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

class NewShoutout implements ShouldQueue
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
        if($notification->notifiable->shouting()->exists())
            $this->SMSContent = getSetting('name') . ": ".$notification->notifiable->shouting->name." gave you a shoutout!.\n".config('app.url') . '/posts/' . $notification->notifiable->listing->id;
        else
            $this->SMSContent = getSetting('name') . ": You've been shouted out!.\n".config('app.url') . '/posts/' . $notification->notifiable->listing->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->notification->user->immediate_notification_method == 'sms' && getsetting('is_shoutout_sms_notifications_enabled'))
        {
            send_sms($this->notification->user->phone, $this->SMSContent);
            $this->updateNotification();
        }
        elseif($this->notification->user->immediate_notification_method == 'email' && EmailNotification::find(4)->is_enabled)
        {
            Mail::to($this->notification->user->email)->send(new \App\Mail\NewShoutout($this->notification->user, $this->notification->notifiable->listing));
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

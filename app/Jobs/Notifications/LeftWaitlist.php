<?php

namespace App\Jobs\Notifications;

use Mail;
use Carbon\Carbon;
use App\Notification;
use App\EmailNotification;
use Illuminate\Bus\Queueable;
use App\Traits\Consolidatable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class LeftWaitlist implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Consolidatable;

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
        $this->SMSContent = getSetting('name') . ": Event spot opened - You're off the waitlist.\n".config('app.url')."/groups/".$notification->notifiable->group->slug."/events/".$notification->notifiable->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->notification->user->notification_frequency == "immediately" && $this->shouldWaitToSend($this->notification))
            $this->consolidate($this->notification);
        elseif($this->notification->user->immediate_notification_method == 'sms' && getsetting('is_event_sms_notifications_enabled'))
        {
            send_sms($this->notification->user->phone, $this->SMSContent);
            $this->updateNotification();
        }
        elseif($this->notification->user->immediate_notification_method == 'email' && EmailNotification::find(5)->is_enabled)
        {
            Mail::to($this->notification->user->email)->send(new \App\Mail\LeftWaitlist($this->notification->user, $this->notification->notifiable));
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
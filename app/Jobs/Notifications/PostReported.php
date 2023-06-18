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

class PostReported implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $notification;

    public $SMSContent;

    public $group;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Notification $notification, $group)
    {
        $this->notification = $notification;
        $this->SMSContent = getSetting('name') . ": A post has been reported.\n".config('app.url');
        $this->group = $group;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->notification->user->immediate_notification_method == 'sms')
        {
            send_sms($this->notification->user->phone, $this->SMSContent);
            $this->updateNotification();
        }
        elseif($this->notification->user->immediate_notification_method == 'email' && EmailNotification::find(7)->is_enabled)
        {
            Mail::to($this->notification->user->email)->send(new \App\Mail\PostReported($this->notification->user, $this->notification->notifiable, $this->group));
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

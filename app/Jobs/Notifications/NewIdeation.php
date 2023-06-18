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

class NewIdeation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Consolidatable;

    public $ideation;

    public $user;

    public $SMSContent;

    public $notification;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Notification $notification)
    {
        $this->ideation = $notification->notifiable;
        $this->user = $notification->user;
        $this->SMSContent = getSetting('name') . ": A new ideation has been posted.\n".config('app.url')."/ideations/".$notification->notifiable->id;
        $this->notification = $notification;
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
        elseif($this->user->immediate_notification_method == 'sms' && getsetting('is_ideation_sms_notifications_enabled'))
        {
            send_sms($this->user->phone, $this->SMSContent);
            $this->updateNotification();
        }
        elseif($this->user->immediate_notification_method == 'email' && EmailNotification::find(10)->is_enabled)
        {
            Mail::to($this->user->email)->send(new \App\Mail\NewIdeation($this->user, $this->ideation));
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

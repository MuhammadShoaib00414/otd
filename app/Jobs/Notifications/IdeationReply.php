<?php

namespace App\Jobs\Notifications;

use Carbon\Carbon;
use App\Notification;
use Illuminate\Bus\Queueable;
use App\Traits\Consolidatable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class IdeationReply implements ShouldQueue
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

        $this->SMSContent = getSetting('name') . ": Someone replied to an ideation.\n".config('app.url')."/ideations/".$notification->notifiable->id;
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
        elseif($this->notification->user->immediate_notification_method == 'sms' && getsetting('is_ideation_sms_notifications_enabled'))
        {
            send_sms($this->notification->user->phone, $this->SMSContent);
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

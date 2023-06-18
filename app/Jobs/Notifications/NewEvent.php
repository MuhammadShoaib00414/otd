<?php

namespace App\Jobs\Notifications;

use Mail;
use Carbon\Carbon;
use App\Notification;
use App\EmailNotification;
use Illuminate\Bus\Queueable;
use App\Traits\Consolidatable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NewEvent implements ShouldQueue
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
        $this->SMSContent = getSetting('name') . ": A new event was created in ".$notification->notifiable->group->name.".\n".config('app.url')."/groups/".$notification->notifiable->group->slug."/events/".$notification->notifiable->id;
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
        elseif($this->notification->user->immediate_notification_method == 'sms')
        {
            send_sms($this->notification->user->phone, $this->SMSContent);
            $this->updateNotification();
        }
        elseif($this->notification->user->immediate_notification_method == 'email' && EmailNotification::find(9)->is_enabled)
        {
            \Log::info("Email notification : " . $this->notification->user->email);
            if ($this->notification->user->deleted_at == null) {
                Mail::to($this->notification->user->email)->send(new \App\Mail\NewEvent($this->notification->user, $this->notification->notifiable));
            }
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

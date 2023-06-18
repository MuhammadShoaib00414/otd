<?php

namespace App\Jobs\Notifications;

use Mail;
use Carbon\Carbon;
use App\Notification;
use App\Traits\Consolidatable;
use App\EmailNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NewPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Consolidatable;

    public $notification;

    public $SMSContent;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Notification $notification, $post_at)
    {
        
        $group = $notification->notifiable->listing->group ?: $notification->notifiable->listing->getGroupFromUser($notification->user_id);
        $this->notification = $notification;
       
        if($group && $notification->notifiable->user()->exists())
            $this->SMSContent = getSetting('name') . ": ".$notification->notifiable->user->name." posted in ".$group->name."\n".config('app.url')."/groups/".$group->slug;
        else
            $this->SMSContent = getSetting('name') . ": New post! \n" . config('app.url').'/posts/'.$notification->notifiable->listing->id;

        $this->post_at = $post_at;
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
        elseif($this->notification->user->immediate_notification_method == 'sms' && getsetting('is_post_sms_notifications_enabled'))
        {
            send_sms($this->notification->user->phone, $this->SMSContent);
            $this->updateNotification();
        }
        elseif($this->notification->user->immediate_notification_method == 'email' && EmailNotification::find(13)->is_enabled)
        {
            Mail::to($this->notification->user->email)->send(new \App\Mail\NewPost($this->notification->user, $this->notification->notifiable));
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

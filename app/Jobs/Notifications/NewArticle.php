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
use App\Traits\Consolidatable;

class NewArticle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Consolidatable;

    public $notification;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Notification $notification)
    {
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
        elseif($this->notification->user->immediate_notification_method == 'email' && EmailNotification::find(17)->is_enabled)
        {
            Mail::to($this->notification->user->email)->send(new \App\Mail\NewArticle($this->notification->user, $this->notification->notifiable));
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

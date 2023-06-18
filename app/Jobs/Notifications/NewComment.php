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
use App\Comment;
class NewComment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Consolidatable;

    public $notification;

    public $SMSContent;
    public $comment;
    public $authUser;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Notification $notification ,$authUser)
    {
       
        $this->notification = $notification;
        $this->authUser = $authUser;
    
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->notification->notifiable->post->user->email)->send(new \App\Mail\NewComment($this->notification->user, $this->notification->notifiable, $this->authUser));
        $this->updateNotification();
    }

    public function updateNotification()
    {
        $this->notification->update([
            'sent_at' => Carbon::now(),
        ]);
    }
}

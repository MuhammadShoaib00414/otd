<?php

namespace App\Listeners\Emailers;

use Mail;
use App\User;
use App\Notification;
use App\Events\PostReported;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailOnPostReported
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(PostReported $event)
    {
        if(!$event->group)
            $users = User::admins()->get();
        elseif($event->group instanceof \App\Group)
            $users = $event->group->admins->merge(User::admins()->get());
        else
            $users = collect([$event->group->owner])->merge(User::admins()->get())->unique('id');

        // foreach($users as $admin)
        // {
        //     Mail::to($admin->email)->send(new \App\Mail\PostReported($event));
        // }

        // $this->checkNotifications($event);
    }

    public function checkNotifications($event)
    {
        Notification::where('notifiable_type', get_class($event->group))->where('notifiable_id', $event->group->id)->where('email_notification_id', 7)->update([
            'sent_at' => \Carbon\Carbon::now(),
        ]);
    }
}

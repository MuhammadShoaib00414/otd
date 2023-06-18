<?php

namespace App\Listeners\Notifications;

use App\Events\IdeationProposed;
use App\Notification;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Queue\InteractsWithQueue;

class NotifyGroupAdminsIdeationProposed implements ShouldQueue
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
    public function handle(IdeationProposed $event)
    {
        $ideation = $event->ideation;
        $groupIds = $ideation->groups->pluck('id');
        $admins = User::whereHas('groups', function (Builder $query) use ($groupIds) {
            $query->whereIn('id', $groupIds)
                  ->where('group_user.is_admin', 1);
        })->get()->merge(User::admins()->get());
        foreach($admins as $admin) {
            Notification::create([
                'notifiable_type' => 'App\Ideation',
                'notifiable_id' => $ideation->id,
                'user_id' => $admin->id,
                'action' => 'Ideation Proposed',
                'email_notification_id' => null,
            ]);
        }
    }
}

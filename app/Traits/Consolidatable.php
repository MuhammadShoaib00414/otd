<?php

namespace App\Traits;

use App\User;
use Carbon\Carbon;
use App\Jobs\Notifications\Feed;

trait Consolidatable
{
    public function shouldWaitToSend($notification)
    {
        $minutesToDelay = 15;
        $user = $notification->user;
        // wait if there were any notifications sent since minutesToDelay
        return $user->notifications()->where('sent_at', '>', now()->subMinutes($minutesToDelay))->whereNotIn('notifiable_type', get_non_consolidatable_notification_types())->where('action', '!=', 'Post Reported')->where('action', '!=', 'Discussion Post Reported')->exists();
    }

    public function consolidate($notification)
    {
        $minutesToDelay = 15;
        Feed::dispatch($notification->user, $notification->id)->delay(now()->addMinutes($minutesToDelay));
    }
}
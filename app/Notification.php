<?php

namespace App;

use App\User;
use Carbon\Carbon;
use App\PushNotification;
use App\Helpers\EmailHelper;
use App\Jobs\NotificationRouter;
use App\Jobs\Notifications\NewPost;
use Illuminate\Support\Facades\Log;
use App\Jobs\Notifications\NewEvent;
use Illuminate\Support\Facades\Mail;
use App\Jobs\Notifications\NewArticle;
use App\Jobs\Notifications\NewComment;
use App\Jobs\Notifications\NewMessage;
use App\Jobs\Notifications\NewIdeation;
use App\Jobs\Notifications\NewShoutout;
use Illuminate\Database\Eloquent\Model;
use App\Jobs\Notifications\LeftWaitlist;
use App\Jobs\Notifications\PostReported;
use App\Jobs\Notifications\UserReported;
use App\Jobs\Notifications\IdeationReply;
use App\Jobs\Notifications\NewDiscussion;
use App\Jobs\Notifications\EventCancelled;
use App\Jobs\Notifications\IdeationInvite;
use App\Jobs\Notifications\DiscussionReply;
use App\Jobs\Notifications\NewIntroduction;
use App\Jobs\Notifications\DiscussionPostReported;
use Codeception\Command\Console;

class Notification extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'notes' => 'array',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'sent_at',
        'read_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function markAsRead()
    {
        $this->update(['viewed_at' => Carbon::now()]);
    }

    public function getUrlAttribute()
    {
        $notifiable = $this->notifiable;
        if ($notifiable instanceof App\MessageThread)
            return "/messages/{$notifiable->id}#last";
        elseif ($notifiable instanceof App\Event) {
            if ($notifiable->group)
                return "/groups/{$notifiable->group->slug}/events/{$this->notifiable_id}";
            else
                return "/events/{$this->notifiable_id}";
        } elseif ($notifiable instanceof App\Ideation) {
            if ($this->action != 'Ideation Not Accepted')
                return "/ideations/{$notifiable->slug}";
            else
                return false;
        } elseif ($notifiable instanceof App\DiscussionThread) {
            if ($notifiable->group()->exists())
                return "/groups/{$notifiable->group->slug}/discussions/{$notifiable->slug}";
            else
                return false;
        } elseif ($notifiable instanceof App\Post)
            if ($notifiable->group()->exists())
                return "/groups/{$notifiable->group->slug}/posts/{$notifiable->id}";
            else
                return false;
        elseif ($notifiable instanceof App\Introduction)
            return "/introductions/{$notifiable->id}";
        elseif ($notifiable instanceof App\Shoutout)
            return "/shoutouts/received";
        else
            $this->markAsRead();
    }

    public function send($when = false, $group = false)
    {
        \Log::info('send function');
        if (!$when)
            $when = Carbon::now();

        $user = $this->user;
        Log::info('count device' . $user->devices->where('active', true)->count());
        $authUser = \Auth::user();


        \Log::info('user device.', ['device' => $user->devices->where('active', true)->count()]);

        

        if ($user->deleted_at === null) {
            if ($user->devices()->where('active', true)->count() > 0) {
                $this->sendPushNotification($user);
            }
        }


        if ($user->notification_frequency != 'immediately')
            return;

        if ($this->notifiable_type == 'App\\MessageThread')
            NewMessage::dispatch($this)->delay($when);
        else if ($this->notifiable_type == 'App\\Event' && $this->action == 'New Event')
            NewEvent::dispatch($this)->delay($when);
        else if ($this->notifiable_type == 'App\\Event' && $this->action == 'Left Waitlist')
            LeftWaitlist::dispatch($this)->delay($when);
        else if ($this->notifiable_type == 'App\\Event' && $this->action == 'Event Cancelled')
            EventCancelled::dispatch($this)->delay($when);
        else if ($this->notifiable_type == 'App\\Ideation' && $this->action == 'New Ideation')
            NewIdeation::dispatch($this)->delay($when);
        else if ($this->notifiable_type == 'App\\Ideation' && $this->action == 'Ideation Invitation')
            IdeationInvite::dispatch($this)->delay($when);
        else if ($this->notifiable_type == 'App\\Ideation' && $this->action == 'Ideation Reply')
            IdeationReply::dispatch($this)->delay($when);
        else if ($this->notifiable_type == 'App\\TextPost' && $this->action == 'New Post')
            NewPost::dispatch($this, $this->notifiable->listing->post_at)->delay($this->notifiable->listing->post_at);
        else if ($this->action == 'Post Reported')
            PostReported::dispatch($this, $group)->delay($when);
        else if ($this->action == 'User Reported')
            UserReported::dispatch($this, $group)->delay($when);
        else if ($this->action == 'New Introduction')
            NewIntroduction::dispatch($this)->delay($when);
        else if ($this->action == 'New Shoutout')
            NewShoutout::dispatch($this)->delay($when);
        else if ($this->action == 'New Discussion')
            NewDiscussion::dispatch($this)->delay($when);
        else if ($this->action == 'Discussion Reply')
            DiscussionReply::dispatch($this)->delay($when);
        else if ($this->action == 'Discussion Post Reported')
            DiscussionPostReported::dispatch($this)->delay($when);
        else if ($this->action == 'New Article Post')
            NewArticle::dispatch($this)->delay($when);
        else if ($this->action == 'Comment on Post')
            NewComment::dispatch($this, $authUser)->delay($when);
    }

    public function sendPushNotification($user)
    {
        if ($this->push_notification_id)

            $this->push_notification->send($user, $this);
    }

    public function getPushNotificationAttribute()
    {
        return PushNotification::find($this->push_notification_id);
    }

    public function getPushNotificationIdAttribute()
    {
        if ($this->notifiable_type == 'App\\MessageThread')
            return 1;
        else if ($this->notifiable_type == 'App\\Event' && $this->action == 'New Event')
            return 7;
        else if ($this->notifiable_type == 'App\\Event' && $this->action == 'Left Waitlist')
            return 4;
        else if ($this->notifiable_type == 'App\\Event' && $this->action == 'Event Cancelled')
            return 5;
        else if ($this->notifiable_type == 'App\\Ideation' && ($this->action == 'Ideation Invitation' || $this->action == 'New Ideation'))
            return 8;
        else if ($this->notifiable_type == 'App\\TextPost' && $this->action == 'New Post')
            return 11;
        else if ($this->notifiable_type == 'App\\ArticlePost')
            return 12;
        else if ($this->action == 'Post Reported' || $this->action == 'Discussion Post Reported')
            return 6;
        else if ($this->action == 'New Introduction')
            return 2;
        else if ($this->action == 'New Shoutout')
            return 3;
        else if ($this->action == 'New Discussion')
            return 9;
        else if ($this->action == 'Discussion Reply')
            return 10;
        else if ($this->action == 'Comment on Post')
            return 13;
        else
            return false;
    }
}

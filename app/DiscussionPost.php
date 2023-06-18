<?php

namespace App;

use App\Like;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class DiscussionPost extends Model
{
    use SoftDeletes, Cachable;

    protected $guarded = ['id'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function thread()
    {
        return $this->belongsTo(DiscussionThread::class, 'discussion_thread_id');
    }

    public function getFormattedBodyAttribute()
    {
        return $this->body;
    }

    public function reported()
    {
        return $this->morphOne('App\ReportedPost', 'postable');
    }

    public function getGroupFromUser($userId)
    {
        return $this->thread->group;
    }

    public function getReportedByAttribute()
    {
        $reportedPost = $this->reported;
        if($reportedPost)
            return $reportedPost->reported_by;
        else
            return false;
    }

    public function getIsReportedAttribute()
    {
        $reportedPost = $this->reported;
        if($reportedPost)
            return $reportedPost->reported_by && !$reportedPost->resolved_by;
        return false;
    }

    public function getResolvedByAttribute()
    {
        $reportedPost = $this->reported;
        if($reportedPost)
            return $reportedPost->resolved_by;
        else
            return false;
    }

    public function isUserAdmin($userId)
    {
        if(\App\User::find($userId)->is_admin)
            return true;

        if($this->thread->group->isUserAdmin($userId))
            return true;

        return false;
    }

    public function notifications()
    {
        return $this->morphMany(\App\Notification::class, 'notifiable');
    }

    public function notificationsFor($userId)
    {
        return $this->morphMany(\App\Notification::class, 'notifiable')->where('user_id', $userId);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'postable');
    }

    public function hasUserLiked($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }
}

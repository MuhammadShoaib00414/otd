<?php

namespace App;

use App\Like;
use App\Comments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Post extends Model
{
    use SoftDeletes, Cachable;

    protected $guarded = ['id'];

    protected $dates = [
        'created_at',
        'updated_at',
        'post_at',
    ];

    public function post()
    {
        return $this->morphTo('post');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function scopeUserPosts($query)
    {
        return $query->whereRaw('id in (select post_id from group_post join group_user on group_user.group_id = group_post.group_id where group_user.user_id = '.auth()->user()->id.')');
    }

    public function scopePublishedPosts($query)
    {
        return $query->whereRaw('`posts`.`id` in (select `post_id` from `group_post` where `group_post`.`group_id` in (select `groups`.`id` from `groups` where `groups`.`publish_to_parent_feed` = 1))');
    }

    public function scopeGroupPosts($query, $groupIds)
    {
        return $query->whereRaw('
            (id in
            (select post_id from group_post where group_id in ('.implode(',', $groupIds).'))
            or posts.group_id in
            ('. implode(',', $groupIds) .')
            )
           ');
    }

    public function getGroupFromUser($userId)
    {
        if(\App\User::find($userId)->is_admin)
            return $this->group ?: $this->groups()->first();

        $groups = $this->groups;
        if($this->group)
            $groups = $groups->merge(collect([$this->group]));
        
        foreach($groups as $group)
        {
            if($group->parent_group_id == null && $group->users()->where('id', $userId)->count())
                return $group;
            else if($group->parent()->count() && $group->parent->users()->where('id', $userId)->count())
                return $group;
        }

        return $groups->first();
    }

     public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
    public function scopeWherePostNotDeleted($query)
    {
        return $query->whereHasMorph('post', [
                        \App\TextPost::class,
                        \App\Event::class,
                        \App\Shoutout::class,
                        \App\ArticlePost::class,
                        \App\DiscussionThread::class,
                    ]);
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

    public function getIsResolvedAttribute()
    {
        $reportedPost = $this->reported;
        if($reportedPost)
            return $reportedPost->resolved_by != null;
        
        return false;
    }

    public function isUserAdmin($user)
    {
        if (!$user instanceof \App\User)
            $user = User::find($user);
        
        if(!$user)
            return false;

        if($user->is_admin)
            return true;

        foreach($this->groups as $group) {
            if($group->isUserAdmin($user->id))
                return true;
        }

        if($this->post->user && $this->post->user->id == ($user instanceof \App\User ? $user->id : $user))
            return true;

        return false;
    }

    public function reported()
    {
        return $this->morphOne('App\ReportedPost', 'postable');
    }

    public function getPhotoUrlAttribute()
    {
        if (! $this->attributes['photo_path'])
            return null;

        return "/uploads/" . $this->attributes['photo_path'];
    }

    public function getPostedByGroupAttribute()
    {
        if(!$this->posted_as_group_id)
            return false;
        else
            return Group::find($this->posted_as_group_id);
    }

    public function getTotalUserCountAttribute()
    {
        $groups = $this->groups()->with('users')->get()->concat([$this->group()->with('users')->get()->first()])->unique('id');
        $users = $groups->pluck('users')->flatten()->merge($this->users)->unique('id');
        return $users->whereNotNull()->count();
    }

    public function logs()
    {
        return $this->morphMany('App\Log','secondary_related_model');
    }

    public function getLinkClickCountAttribute()
    {
        return $this->logs()->where('action', 'clicked post link')->count();
    }

    public function notifications()
    {
        return $this->morphMany(\App\Notification::class, 'notifiable');
    }

    public function notificationsFor($userId)
    {
        return $this->morphMany(\App\Notification::class, 'notifiable')->where('user_id', $userId);
    }

    public function getUrlAttribute()
    {
        if(!$this->group)
            return '/posts/'.$this->id;
        return '/groups/'.$this->group->slug.'/posts/'.$this->id;
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'postable');
    }

    public function hasUserLiked($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function comments()
    {
        return $this->hasMany(Comments::class, 'post_id');
    }
}

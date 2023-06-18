<?php

namespace App;

use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Ideation extends Model implements Searchable
{
    use SoftDeletes, Cachable;
    
    protected $guarded = ['id'];

    protected $casts = [
        'localization' => 'array',
    ];

    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'ideation_user')->withPivot('user_id', 'ideation_id', 'viewed_at');
    }
    public function shouting()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function invitations()
    {
        return $this->hasMany(IdeationInvitation::class);
    }

    public function posts()
    {
        return $this->hasMany(IdeationPost::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function proposer()
    {
        return $this->belongsTo(User::class, 'proposed_by_id');
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function videoRoom()
    {
        return $this->morphOne(VideoRoom::class, 'attachable');
    }

    public function articles()
    {
        return $this->hasMany(IdeationArticle::class);
    }

    public function surveys()
    {
        return $this->hasMany(IdeationSurvey::class);
    }

    public function getInvitedUsersAttribute()
    {
        return User::whereIn('id', $this->invitations()->pluck('user_id'))->get();
    }

    public function currentUserInvitation($user_id, $ideation_id)
    {
        return $this->invitations()->where('user_id', $user_id)->where('ideation_id', $ideation_id)->first();
    }

    public function getHasMaxParticipantsAttribute()
    {
        if($this->max_participants && $this->max_participants > 0 && $this->participants()->count() >= $this->max_participants)
            return true;
        else
            return false;
    }

    public function getIsJoinableAttribute()
    {
        return !$this->has_max_participants && $this->is_approved;
    }

    public function notifications()
    {
        return $this->morphMany(\App\Notification::class, 'notifiable');
    }

    public function notificationsFor($userId)
    {
        return $this->morphMany(\App\Notification::class, 'notifiable')->where('user_id', $userId);
    }

    public function scopeNotFull($query)
    {
	return $query->where(function ($query) {
		$query->whereRaw('ideations.max_participants is null or ideations.max_participants > (select count(*) from `users` inner join `ideation_user` on `users`.`id` = `ideation_user`.`user_id` where `ideations`.`id` = `ideation_user`.`ideation_id` and `users`.`deleted_at` is null)');
	});
    }

    public function getIsCurrentUserParticipantAttribute()
    {
        $user = Auth::user();
        if($user->is_admin)
            return true;

        return $this->participants()->where('user_id', $user->id)->count();
    }

    public function getReportedPostsAttribute()
    {
        return $this->posts()->whereHas('reported')->get();
    }

    public function getReportedCountAttribute()
    {
        return $this->reported_posts_count + $this->reported_articles_count;
    }

    public function getReportedArticlesCountAttribute()
    {
        return $this->articles()->whereHas('reported', function($query) {
             $query->whereNull('resolved_by');
        })->count();
    }

    public function getReportedPostsCountAttribute()
    {
        return $this->posts()->whereHas('reported', function($query) {
             $query->whereNull('resolved_by');
        })->count();
    }

    public function getActiveUsersAttribute()
    {
        return \App\User::whereIn('id', $this->posts()->pluck('user_id'))->get();
    }

    public function getSearchResult(): SearchResult
     {
        $url = '/ideations/' . $this->slug;
     
         return new \Spatie\Searchable\SearchResult(
            $this,
            $this->name,
            $url
         );
     }

    public function getNameAttribute($value)
    {
        return localizedValue('name', $this->localization) ?: $value;
    }
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}

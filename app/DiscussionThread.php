<?php

namespace App;

use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class DiscussionThread extends Model implements Searchable
{
    use SoftDeletes, Cachable;

    protected $guarded = ['id'];

    protected $casts = [
        'localization' => 'array',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function posts()
    {
        return $this->hasMany(DiscussionPost::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }
    
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function listing()
    {
        return $this->morphOne(Post::class, 'post');
    }
    
    public function getParticipantsAttribute()
    {
        return $this->posts->pluck('user')->unique()->flatten();
    }

    public function getHasReportedAttribute()
    {
        $reportedPosts = $this->posts()->whereHas('reported', function ($query) {
            $query->whereNull('resolved_by');
        })->get();

        return $reportedPosts->length();
    }

    public function getReportedPostsAttribute()
    {
        $reportedPosts = $this->posts()->whereHas('reported', function ($query) {
            $query->whereNull('resolved_by');
        })->get();

        return $reportedPosts;
    }

    public function getHasResolvedAttribute()
    {
        foreach($this->posts as $post)
        {
            if($post->resolved_by)
                return true;
        }
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

    public function getSearchResult(): SearchResult
    {
        $url = '/groups/' . $this->group->slug . '/discussions/' . $this->slug;
     
         return new \Spatie\Searchable\SearchResult(
            $this,
            $this->name,
            $url
         );
    }

    public function getLastPostAttribute()
    {
        return $this->posts()->orderBy('created_at', 'desc')->first();
    }

    public function getNameAttribute($value)
    {
        return localizedValue('name', $this->localization) ?: $value;
    }

    public function getRecentPostsAttribute()
    {
        if(!$this->posts()->exists())
            return collect([]);
        return $this->posts()->orderBy('created_at', 'desc')->whereHas('user')->with('user')->limit(3)->get()->reverse();
    }

    public function revertTimestamp()
    {
        if(!$this->posts()->exists())
            return;
        $this->listing()->update([
            'post_at' => $this->posts()->orderBy('created_at', 'desc')->first()->created_at,
        ]);
    }

    //TODO
    public function localize($locale)
    {
        return $this;
        if(!$locale)
            return $this;

        if(!array_key_exists($locale, $this->localization))
            return $this;

        foreach($this->localization[$locale] as $localizedAttribute => $localizedValue)
        {
            $this[$localizedAttribute] = $localizedValue;
        }

        return $this;
    }
}

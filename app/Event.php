<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Event extends Model implements Searchable
{

    use SoftDeletes, Cachable;

    protected $guarded = ['id'];

    protected $dates = [
        'date',
        'end_date',
        'recur_until',
    ];

    protected $casts = [
        'custom_menu' => 'json',
        'localization' => 'array',
    ];

    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    public function listing()
    {
        return $this->morphOne(Post::class, 'post');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function owningGroup()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function getImagePathAttribute()
    {
        return getS3Url($this->image);
    }

    public function eventRsvps()
    {
        return $this->hasMany(EventRsvp::class);
    }

    public function attending()
    {
        return $this->belongsToMany(User::class, 'event_rsvps')->where('response', '=', 'yes');
    }

    public function notAttending()
    {
        return $this->belongsToMany(User::class, 'event_rsvps')->where('response', '=', 'no');
    }

    public function waitlist()
    {
        return $this->hasMany(WaitlistedUser::class);
    }

    public function getRecurDaysAttribute()
    {
        $recur_days_map = [
            '' => 0,
            'day' => 1,
            'week' => 7,
            'month' => 30, //this will be problematic
        ];

        return $recur_days_map[$this->recur_every];
    }

    public function getDateAttribute($value)
    {
        if(!$this->recur_every || !$this->has_happened)
            return Carbon::parse($this->getRawOriginal('date'));

        $days_to_add = $this->recur_days;

        //if the next recurrance is greater than recur_until, stop recurring
        if($this->recur_until && Carbon::parse($this->getRawOriginal('date'))->addDays($days_to_add)->gt($this->recur_until->endOfDay()))
            return Carbon::parse($this->getRawOriginal('date'));

        if($this->recur_every == 'week' && $this->has_happened)
        {
            $date = Carbon::parse($this->getRawOriginal('date'))->addDays($days_to_add);
            // Commented due to the org-demo error
            $this->update([
                'date' => $date->toDateTimeString(),
                'end_date' => $this->end_date->addDays($days_to_add)->toDateTimeString(),
            ]);

            return $date;
        }


        return Carbon::parse($this->getRawOriginal('date'));
    }

    public function getWaitlistUsersAttribute()
    {
        return $this->waitlist->map(function ($waitlistedUser) {
            return $waitlistedUser->user;
        });
    }

    public function isUserRSVPd($user = null)
    {
        if ($user == null)
            $user = request()->user();

        return $this->eventRsvps()->where('user_id', '=', $user->id)->count();
    }

    public function rsvpFor($userId)
    {
        return $this->eventRsvps()->where('user_id', '=', $userId)->first();
    }

    public function isUserWaitlisted($user = null)
    {
        if ($user == null)
            $user = request()->user();

        return $this->waitlist()->where('user_id', '=', $user->id)->count();
    }

    public function getUserLabel($user)
    {
        if ($this->isUserWaitlisted($user))
            return " (on waitlist)";
        else if ($this->isUserRSVPd($user))
            return " (RSVP'd)";
        else
            return "";
    }

    public function isGroupInvited($groupId)
    {
        return $this->groups()->where('group_id', $groupId)->count();
    }

    public function popWaitlist()
    {
        $waitlistItem = $this->waitlist()->orderBy('created_at', 'asc')->first();

        if ($waitlistItem && $waitlistItem->user) {
            $this->waitlist()->where('user_id', $waitlistItem->user->id)->delete();

            $rsvp = \App\EventRsvp::create([
                'user_id' => $waitlistItem->user->id,
                'event_id' => $this->id,
                'response' => 'yes',
            ]);

            event(new \App\Events\LeftWaitlist($waitlistItem->user->id, $this));
        }
    }

    public function bulkPopWaitlist($count)
    {
        for($i = 0; $i < $count; $i++)
            $this->popWaitlist();
    }

    public function getHasMaxParticipantsAttribute()
    {
        if(!$this->max_participants)
            return false;
        else if($this->max_participants <= $this->attending()->count())
            return true;
        else
            return false;
    }

    public function getFormattedBodyAttribute()
    {
        $body = $this->description;
        $url = '@(http)?(s)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
        $body = preg_replace($url, '<a href="http$2://$4" target="_blank" title="$0">$0</a>', $body);
        $body = nl2br($body);

        return $body;
    }

    public function threads()
    {
        return $this->hasMany(\App\MessageThread::class);
    }

    public function hasThread(String $type)
    {
        return $this->threads()->where('type', $type)->count();
    }

    public function syncMessageThreads()
    {
        if($this->threads()->count())
        {
            $groupAdminIds = $this->group->admins()->pluck('users.id');
            foreach($this->threads as $thread)
            {
                if($thread->type == "attending")
                {
                    $attendingIds = $this->attending()->pluck('users.id');
                    $ids = $attendingIds->union($groupAdminIds);
                    $thread->participants()->sync($ids);
                }
                else if($thread->type == "interested")
                {
                    $interestedIds = $this->notAttending()->pluck('users.id');
                    $ids = $interestedIds->union($groupAdminIds);
                    $thread->participants()->sync($ids);
                }
                else if($thread->type == "waitlisted")
                {
                    $waitlistedIds = $this->waitlist()->pluck('users.id');
                    $ids = $waitlistedIds->union($groupAdminIds);
                    $thread->participants()->sync($ids);
                }
            }
        }
    }

    public function getGroupFromUser($userId)
    {
        $groups = $this->groups;
        if($this->group)
            $groups = $groups->merge(collect([$this->group]));
        $possibleGroup = false;
        foreach($groups as $group)
        {
            if($group->users()->where('id', $userId)->count() || \App\User::find($userId)->is_admin)
            {
                if(!$group->parent)
                    return $group;
                else
                    $possibleGroup = $group;
            }
        }

        if($possibleGroup)
            return $possibleGroup;

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

    public function getHasHappenedAttribute()
    {
        return ($this->end_date) ? $this->end_date->isPast() : $this->date->addDays(1)->isPast();
    }

    public function getIsLiveAttribute()
    {
        if ($this->end_date == null)
            return false;

        if ($this->date->isPast() && $this->end_date->isFuture())
            return true;

        return false;
    }

    public function getCustomLinksAttribute()
    {
        if(getsetting('is_localization_enabled') && isset($this->custom_menu))
        {
            $custom_links = [];
            $count = 0;
            $locale = \Illuminate\Support\Facades\App::getLocale();

            foreach($this->custom_menu as $link)
            {
                $hasLocalizedValue = isset($this->localization[$locale]['links'][$count]['title']);
                $parsedLink['title'] = $hasLocalizedValue ? $this->localization[$locale]['links'][$count]['title'] : $link['title'];
                $parsedLink['url'] = $link['url'];
                $custom_links[] = $parsedLink;
                $count++;
            }

            return $custom_links;
        }
        return $this->custom_menu;
    }

    public function getInvitedUserIdsAttribute()
    {
        return $this->eventRsvps()->whereNull('response')->pluck('user_id');
    }

    public function getInvitedUsersAttribute()
    {
        return \App\User::whereIn('id', $this->invited_user_ids)->orderBy('name', 'asc')->get();
    }

    public function isUserInvited($userId)
    {
        return $this->invited_user_ids->contains($userId);
    }

    public function getSearchResult(): SearchResult
    {
        $url = '/groups/' . $this->getGroupFromUser(Auth::user()->id)->slug . '/events/' . $this->id;
     
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

    public function getNameRawAttribute()
    {
        return $this->attributes['name'];
    }

    public function getDescriptionAttribute($value)
    {
        return localizedValue('description', $this->localization) ?: $value;
    }

    public function getDescriptionRawAttribute()
    {
        return $this->attributes['description'];
    }

    public function getCancelledReasonAttribute($value)
    {
        return localizedValue('cancelled_reason', $this->localization) ?: $value;
    }

    public function localizedLinkTitle($index, $locale)
    {
        if(getsetting('is_localization_enabled'))
        {
            if(isset($this->custom_menu[$index]['title']))
                if(isset($this->localization[$locale]['links'][$index]['title']))
                    return $this->localization[$locale]['links'][$index]['title'];
        }
    }

    public function linkTitleRaw($index)
    {
        if(isset($this->custom_menu[$index]['title']))
            return $this->custom_menu[$index]['title'];

        return false;
    }
}

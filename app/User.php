<?php

namespace App;

use App\Like;
use App\Event;
use App\Taxonomy;
use App\Receipt;
use Carbon\Carbon;
use Laravel\Cashier\Billable;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class User extends Authenticatable implements Searchable
{
    use Billable;
    use Notifiable;
    use SoftDeletes;
    use Cachable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'is_enabled' => 'boolean',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'employment_start_date',
        'birthday',
    ];

    public function optionsForTaxonomy(Taxonomy $taxonomy)
    {
        return $this->options()->where('taxonomy_id', '=', $taxonomy->id)->where('is_enabled', 1)->orderBy('name', 'asc')->get();
    }

    public function options()
    {
        return $this->belongsToMany(Option::class);
    }

    public function hasOption($optionId)
    {
        return $this->options()->where('options.id', $optionId)->count();
    }

    public function getMostPopulatedOptionsAttribute()
    {
        return $this->options()->withCount('users')->get()->sortByDesc('users_count');
    }

    public function threads()
    {
        return $this->belongsToMany(MessageThread::class, 'message_participants')->whereNull('message_participants.deleted_at')->withPivot('left_at');
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function ideations()
    {
        return $this->belongsToMany(Ideation::class, 'ideation_user');
    }

    public function awardedPoints()
    {
        return $this->hasMany(AwardedPoint::class);
    }
    
    public function messages()
    {
        return $this->hasManyThrough(Message::class, MessageThread::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sending_user_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class)->orderBy('categories.name', 'asc');
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class)->orderBy('skills.name', 'asc');
    }

    public function keywords()
    {
        return $this->belongsToMany(Keyword::class)->orderBy('keywords.name', 'asc');
    }

    public function workWanting()
    {
        return $this->belongsTo(Work::class, 'work_wanting');
    }

    public function workOffering()
    {
        return $this->belongsTo(Work::class, 'work_offering');
    }

    public function introductions()
    {
        return $this->belongsToMany(Introduction::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class)->withPivot('is_admin');
    }

    public function groupsName() {
        return $this->belongsToMany(Group::class)->pluck('name');
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class);
    }

    public function optionBadges(bool $showPrivate = false)
    {
        return $this->options()->whereHas('taxonomy', function($q) use ($showPrivate) {
            $q = $q->where('taxonomies.is_enabled', 1)->where('taxonomies.is_badge', 1);
          
            if(!$showPrivate)
                $q = $q->where('taxonomies.is_public', 1);
            return $q;
        })->get();
    }

    public function allBadges(bool $showPrivate = false)
    {
        $q = $this->badges()->distinct();
      
        if(!getsetting('is_ask_a_mentor_enabled'))
            $q->where('id', '!=', 5);
          
          
       
        return $q->get()->concat($this->optionBadges($showPrivate));
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }

    public function events()
    {
        return $this->hasManyThrough('App\Event', 'App\Group');
    }

    public function rsvps()
    {
        return $this->hasMany(EventRsvp::class);
    }

    public function shoutouts()
    {
        return $this->hasMany(Shoutout::class, 'shoutout_by');
    }

    public function receivedShoutouts()
    {
        return $this->hasMany(Shoutout::class, 'shoutout_to');
    }

    public function titles()
    {
        return $this->belongsToMany(Title::class)
                    ->using(TitleUser::class)
                    ->withPivot([
                        'assigned_id',
                    ]);
    }

    public function ideationInvitations()
    {
        return $this->hasMany(IdeationInvitation::class);
    }

    // public function getUnreadMessageCountAttribute()
    // {
    //     return \DB::select('select count(*) as total from message_participants inner join messages on message_participants.message_thread_id=messages.message_thread_id where message_participants.user_id = ? and message_participants.deleted_at is null and messages.recipient_read_at is null and sending_user_id != ?', [$this->id, $this->id])[0]->total;
    // }

    public function getUnreadIntroductionCountAttribute()
    {
        return $this->notifications()->where('notifiable_type', 'App\\Introduction')->distinct('notifiable_id')->whereNull('viewed_at')->count();
    }

    public function awardPoint($key)
    {
        $point = Point::where('key', '=', $key)->first();
        AwardedPoint::create([
            'user_id'  => $this->id,
            'point_id' => $point->id,
            'points'   => $point->value,
        ]);
        $this->points_ytd += $point->value;
        $this->points_total += $point->value;
        $this->save();
    }

    public function points()
    {
        return Point::whereIn('id', $this->awardedPoints()->pluck('point_id'))->get(); 
    }

    public function ownedEvents()
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    public function getPhotoPathAttribute()
    {
        if (! $this->attributes['photo_path'])
            return '/images/profile-icon-empty.png';

        return getS3Url($this->attributes['photo_path']);
    }

    public function scopeVisible($query)
    {
        return $query->where('users.is_enabled', '=', '1')
                    ->where('users.is_hidden', '=', '0');
    }

    public function updateSearch()
    {
        $this->search = implode(' ', $this->options()->where('is_enabled', 1)->whereHas('taxonomy')->pluck('name')->toArray());
        $this->save();
    }


    public function getUnreadIdeationInvitationsAttribute()
    {
        return $this->notifications()->where('notifiable_type', 'App\\Ideation')->whereNull('viewed_at')->distinct('notifiable_id')->get()->filter(function($notification) {
            return $notification->notifiable && !$notification->notifiable->deleted_at;
        });
    }

    public function getIsManagerAttribute()
    {
        return \DB::table('title_user')->where('assigned_id', '=', $this->id)->count() > 0;
    }

    public function getHasOtherGenderPronounAttribute()
    {
        return !($this->gender_pronouns == "He/Him/His" || $this->gender_pronouns == "She/Her/Hers" || $this->gender_pronouns == "They/Them");
    }

    public function getFeedPostGroupsAttribute()
    {
        $key = 'user_' . $this->id . '_groups';
        if(\Cache::has($key))
            return \Cache::get($key);

        return \Cache::remember($key, 7200, function () {
            return flattenGroupTrees(
                $this->groups()
                     ->where(function ($query) {
                        $query->whereNull('parent_group_id')
                              ->where('publish_to_parent_feed', 1);
                          })
                     ->with('publishable_subgroups_recursive')->get()
                );
        });
    }

    public function getDashboardFeedPostGroupsAttribute()
    {
        return $this->groups()->where('publish_to_dashboard_feed', 1)->get();
    }

    public function getDashboardPostsAttribute()
    {
        $groups = $this->dashboard_feed_post_groups->pluck('id');
        $blockedByMe = ReportedUsers::where('reported_by', $this->id)->where('status', 'blocked')->pluck('user_id')->toArray();
        $whoBlockedMe = ReportedUsers::where('user_id', $this->id)->where('status', 'blocked')->pluck('reported_by')->toArray();
        $blockedUserIds = array_merge($blockedByMe, $whoBlockedMe);
        $groupPostIds = \App\Post::where(function ($q) use ($groups) {
            $q->whereHas('groups', function (Builder $query) use ($groups) {
                            return $query->whereIn('groups.id', $groups)
                                  ->whereNull('groups.deleted_at');
                        })
                        ->orWhereHas('group', function (Builder $query) use ($groups) {
                            return $query->whereIn('groups.id', $groups)
                                ->whereNull('groups.deleted_at');
                        });
        });

        $groupPostIds = $groupPostIds->pluck('id');
        $postIds = $groupPostIds->merge($this->userPosts()->pluck('id'))->unique();
        
        $posts = \App\Post::whereIn('id', $postIds)
                        ->where('posts.is_enabled', 1)
                        ->with(['groups', 'group'])
                        ->withCount('likes')
                        ->where('post_at', '<=', \Carbon\Carbon::now())
                        ->whereHasMorph('post', [
                            \App\TextPost::class,
                            \App\Event::class,
                            \App\Shoutout::class,
                            \App\ArticlePost::class,
                            \App\DiscussionThread::class,
                        ], function (Builder $query) use ($blockedUserIds) {
                                $query->whereNull('deleted_at');
                                // $query->whereHas('user', function (Builder $query) use ($blockedUserIds) {
                                //     $query->whereNotIn('users.id', $blockedUserIds);
                                // });
                                $table = $query->getModel()->getTable(); 
                                if ($table == 'shoutouts') {
                                    $query->whereNotIn('shoutout_by', $blockedUserIds);
                                } else if ($table == 'events') {
                                    $query->whereNotIn('created_by', $blockedUserIds);
                                } else {
                                    $query->whereNotIn('user_id', $blockedUserIds);
                                }
                        })->orderBy('post_at', 'desc');
        if (request()->has('since'))
            $posts = $posts->where('post_at', '>=', request()->since);

        return $posts->paginate(8);
    }

    public function getFeedPostsAttribute()
    {
        $groups = $this->feed_post_groups->pluck('id');

        $groupPostIds = \App\Post::whereHas('groups', function (Builder $query) use ($groups) {
                            return $query->whereIn('id', $groups)
                                  ->whereNull('groups.deleted_at');
                        })
                        ->where('post_at', '<=', \Carbon\Carbon::now())
                        ->pluck('id');

        $postIds = $groupPostIds->merge($this->userPosts()->where('posts.post_at', '<=', \Carbon\Carbon::now())->pluck('id'))->unique();

        return \App\Post::whereIn('id', $postIds)
                        ->whereHasMorph('post', [
                            \App\TextPost::class,
                            \App\Shoutout::class,
                            \App\Event::class,
                            \App\ArticlePost::class,
                            \App\DiscussionThread::class,
                        ])
                        ->orderBy('post_at', 'desc')->simplePaginate(8);
    }

    public function getDashboardGroupsAttribute()
    {
        return $this->groups()->whereNull('parent_group_id')->orderBy('dashboard_order_key')->get()->unique()->groupBy('dashboard_header');
    }

    public function getDashboardGroupsRecursiveAttribute()
    {
        return $this->groups()->whereNull('parent_group_id')->orderBy('dashboard_order_key')->with('dashboard_subgroups_recursive')->get()->unique()->groupBy('dashboard_header');
    }

    public function localized_dashboard_groups_recursive($locale)
    {
        $dashboard_groups = $this->groups()->whereNull('parent_group_id')->orderBy('dashboard_order_key')->get()->unique()->groupBy('dashboard_header');
        $return = [];

        foreach($dashboard_groups as $header => $groups)
        {
            $groups = $groups->map(function($group) use ($locale) {
                $group = $group->localize($locale);
                if($group->subgroups()->exists())
                    $group->subgroups = $group->localized_subgroups_recursive($locale);
                return $group;
            });
            $return[$header] = $groups;
        }

        return $return;
    }

    public function textPosts()
    {
        return $this->hasMany(\App\TextPost::class);
    }

    public function discussionPosts()
    {
        return $this->hasMany(\App\DiscussionPost::class);
    }

    public function discussionThreads()
    {
        return $this->hasMany(\App\DiscussionThread::class);
    }

    public function getDashboardEventsAttribute()
    {
        $groups = $this->groups()->get();
        $groups = $groups->merge(Group::whereIn('parent_group_id', $groups->pluck('id'))->where('publish_to_parent_feed', 1)->get());
        $events = $groups->pluck('allEvents')->flatten();

        $events = $events->merge(Event::whereIn('id', $this->rsvps()->pluck('event_id'))->get())->unique()->where('is_cancelled', 0);

        return $events;
    }

    public function getAllEventsAttribute()
    {
        $events = $this->groups->pluck('ownedEvents')->flatten();

        $events = $events->merge(Event::whereIn('id', $this->rsvps()->pluck('event_id'))->get())->unique();

        return $events;
    }

    public function notifications()
    {
        return $this->hasMany(\App\Notification::class);
    }

    public function unprocessedNotifications()
    {
        return $this->notifications()->whereNull('viewed_at')->whereNull('sent_at');
    }

    public function unsentNotifications()
    {
        return $this->notifications()->whereNull('sent_at');
    }

    public function getUnreadMessageCountAttribute()
    {
        return $this->threads->load('messages')->filter(function($thread) {
            return $thread->is_unread;
        })->count();
    }

    public function getEventNotificationsCountAttribute()
    {
        return $this->unreadNotifications()->where('notifiable_type', 'App\\Event')->unique('notifiable_id')->count();
    }

    public function getEventsWithNotificationsAttribute()
    {
        return Event::whereIn('id', $this->unreadNotifications()->where('notifiable_type', 'App\\Event')->unique('notifiable_id')->pluck('notifiable_id'))->orderBy('id', 'desc')->get();
    }

    public function getGroupOnlyAttribute()
    {
        if($this->group_only_id)
            return Group::find($this->group_only_id);
        return false;
    }

    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('viewed_at')->whereHasMorph('notifiable', [
            \App\TextPost::class,
            \App\Shoutout::class,
            \App\Event::class,
            \App\ArticlePost::class,
            \App\DiscussionThread::class,
            \App\MessageThread::class,
            \App\Post::class,
        ])->get();
    }

    public function questions()
    {
        return $this->belongsToMany(\App\Question::class)->withPivot('answer');
    }

    public function getUserQuestionsAttribute()
    {
        return $this->questions()->pluck('question_user.answer', 'questions.question');
    }

    public function getUnreadShoutoutCountAttribute()
    {
        return $this->unreadNotifications()->where('notifiable_type', 'App\\Shoutout')->groupBy('notifiable_id')->count();
    }

    public function getCategoriesWithMostUsersAttribute()
    {
        return $this->categories()->withCount('users')->get()->sortByDesc('users_count');
    }

    public function scopeAdmins($query)
    {
        return $query->where('is_admin', 1);
    }

    public function orderedOptionsForTaxonomy($taxonomy)
    {
        return $this->optionsForTaxonomy($taxonomy)->sortBy(function ($option) {
            return $option->orderKey('profile');
        });
    }

    public function getVisiblePlatformUsersAttribute()
    {
        if($this->is_admin)
            return User::visible()->orderBy('name')->get();

        $groups = $this->groups;
        $users = collect([]);
        foreach($groups as $group)
        {
            $users = $users->merge($group->users()->get());
        }

        return $users;
    }

    public function getVisiblePlatformGroupsAttribute()
    {
        if($this->is_admin)
            return Group::orderBy('name', 'asc')->get();
        else
            return $this->groups;
    }

    public function getGroupsAdminOfAttribute()
    {
        return $this->groups()->where('is_admin', 1)->get();
    }

    public function getIsGroupAdminAttribute()
    {
        return $this->groups_admin_of->count();
    }

    public function userPosts()
    {
        return $this->belongsToMany(Post::class, 'post_user');
    }

    public function getSearchResult(): SearchResult
    {
        $url = '/users/' . $this->id;
     
         return new \Spatie\Searchable\SearchResult(
            $this,
            $this->name,
            $url
         );
    }

    public function getIsSuperAdminAttribute()
    {
        return $this->email == 'davis@ipx.org' || $this->email == 'cm@ipx.org' || $this->email == 'lbannonnn7@gmail.com' || $this->email == 'shayannshahid@gmail.com' || $this->getRawOriginal('is_super_admin');
    }

    public function getIsAdminAttribute($value)
    {
        return $this->is_super_admin || $value;
    }

    public function getImmediateNotificationMethodAttribute()
    {
        if($this->notification_frequency == 'immediately')
        {
            //temp until sms is re-enabled
            return 'email';

            if($this->notification_method == 'sms' && $this->phone)
                return 'sms';
            else
                return 'email';
        }

        return false;
    }

    public function getShouldSmsAttribute()
    {
        return !$this->notifications()->where('sent_at', '>', Carbon::now()->subMinutes(10))->exists();
    }

    public function getPhoneAttribute($value)
    {
        return ($value != '+1' && preg_match("/^\d{3}\d{3}\d{4}$/", substr(preg_replace('/[^0-9]/', '', $value), 1))) ? $value : false;
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

  

    public function hasLiked($postable_type, $postable_id)
    {
        return $this->likes()->where('postable_type', $postable_type)->where('postable_id', $postable_id)->exists();
    }

    public function getShouldSendPushNotificationAttribute()
    {
        return isset($this->device_token) && $this->enable_push_notifications;
    }

    public function getLocaleAttribute($value)
    {
        if(!getsetting('is_localization_enabled'))
            return 'en';
        return $value;
    }

    public function hasBoughtTicket($ticketId)
    {
        return $this->receipts()->where('ticket_id', $ticketId)->count();
    }

    public function hasBoughtTicketForRegistrationPage($pageId)
    {
        $ticketIds = \App\RegistrationPage::find($pageId)->tickets()->pluck('id');
        return $this->receipts()->whereIn('ticket_id', $ticketIds)->count();
    }

    public function getReceiptUrl($pageId)
    {

        $ticketIds = \App\RegistrationPage::find($pageId)->tickets()->pluck('id');
        $receipt = $this->receipts()->whereIn('ticket_id', $ticketIds)->first();

        if(!$receipt)
            return '/';

        return '/purchases/' . $receipt->id;
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }
    
    public function blockedUsers($count = null)
    {
        $userIds = ReportedUsers::where('reported_by', $this->id)->where('status', 'blocked')->pluck('user_id');
        $query = User::WhereIn('id', $userIds);
        $users = ($count == null) ? $query->get() : $query->limit($count)->get();
        return $users;
    }
    public function getTrackInfo($value)
    {
        return json_decode($value,true);
    }
}

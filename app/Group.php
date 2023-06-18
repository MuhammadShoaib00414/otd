<?php

namespace App;

use App\Post;
use Carbon\Carbon;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Group extends Model implements Searchable
{
    use SoftDeletes, Cachable;
    
    protected $guarded = ['id'];

    protected $casts = [
        'localization' => 'array',
        'post_order' => 'array',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function sequence()
    {
        return $this->belongsTo(Sequence::class);
    }

    public function allUsers()
    {
        return $this->belongsToMany(User::class)->withPivot('is_admin')->distinct('id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->where('is_enabled', 1)->withPivot('is_admin')->distinct('id');
    }
    

    public function activeUsers()
    {
        return $this->belongsToMany(User::class)->where('is_enabled', '=', 1)->where('is_hidden', '=', 0)->withPivot('is_admin')->orderBy('name', 'asc')->distinct('id');
    }

    public function chatRoom()
    {
        return $this->morphOne(ChatRoom::class, 'attachable');
    }

    public function videoRoom()
    {
        return $this->morphOne(VideoRoom::class, 'attachable');
    }

    public function emailCampaigns()
    {
        return $this->hasMany(EmailCampaign::class);
    }

    public function admins()
    {
        return $this->users()->where('group_user.is_admin', '=', 1)->where('users.is_enabled', 1)->where('users.is_hidden', 0)->distinct('user_id');
    }

    // public function virtualRoom()
    // {
    //     return $this->hasOne(VirtualRoom::class)->where('is_mobile', 0);
    // }

    public function getVirtualRoomAttribute()
    {
        return VirtualRoom::where('group_id', $this->id)->where('is_mobile', 0)->with('clickAreas')->first();
    }

    public function getMobileVirtualRoomAttribute()
    {
        return VirtualRoom::where('group_id', $this->id)->where('is_mobile', 1)->with('clickAreas')->first();
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class)->whereNotIn('post_type', $this->getDisabledContentTypes());
    }

    public function is_zoom_enabled()
    {
        return is_zoom_enabled() && $this->zoom_meeting_id;
    }

    public function getOrderedPostIdsAttribute()
    {
        return $this->post_order;
    }

    public function localize($locale)
    {
        if($locale == 'en')
            return $this;

        if(!$this->localization || !array_key_exists($locale, $this->localization))
            return $this;

        foreach($this->localization[$locale] as $localizedAttribute => $localizedValue)
        {
            if($localizedValue)
                $this[$localizedAttribute] = $localizedValue;
        }

        return $this;
    }

    public function localized_subgroups_recursive($locale)
    {
        $subgroups = $this->subgroups()->where('should_display_dashboard', 1)->get();

        $subgroups = $subgroups->map(function($subgroup) use ($locale) {
            $subgroup = $subgroup->localize($locale);
            if($this->subgroups()->exists())
                $subgroup->subgroups = $subgroup->localized_subgroups_recursive($locale);
            return $subgroup;
        });

        return $subgroups;
    }

    public function resetOrderedPosts()
    {
        if(isset($this->post_order_last_set_at) && count($this->post_order))
        {
            
            $post_order = $this->post_order;
            $post_order = Post::whereIn('id', $this->post_order)->where('posts.post_at', '<', $this->post_order_last_set_at)->pluck('id');
            $newPosts = $this->posts()->where('posts.post_at', '>', $this->post_order_last_set_at)->whereNotIn('id', $post_order)->orderBy('post_at', 'desc')->get();

            $newSubgroupPosts = Post::where('post_at', '>', $this->post_order_last_set_at)->groupPosts($this->viewable_group_ids)->whereNotIn('posts.id', $newPosts->pluck('id'))->orderBy('post_at', 'desc')->get();

            $newPosts = $newPosts->concat($newSubgroupPosts)->unique('id')->sortByDesc('post_at')->pluck('id');

            $orderedPosts = Post::whereIn('id', $this->post_order)->orderByRaw("FIELD(id, ".implode(',', $this->post_order).")")->pluck('id');

            $oldPosts = $this->posts()->where('posts.post_at', '<', $this->post_order_last_set_at)->orderBy('post_at', 'desc')->pluck('id');

            $orderedPostIds = $newPosts->concat($orderedPosts)->concat($oldPosts)->unique();

            $this->update([
                'post_order' => $orderedPostIds->take(49),
                'post_order_last_set_at' => Carbon::now(),
            ]);
            return;
        }
        
        $orderedPostIds = Post::groupPosts($this->viewable_group_ids)->orderBy('post_at', 'desc')->pluck('id');
        $this->update([
            'post_order' => $orderedPostIds->take(49),
            'post_order_last_set_at' => Carbon::now(),
        ]);

        if($this->parent_group_id)
            $this->parent->resetOrderedPosts();
    }

    public function shoutouts()
    {
        return $this->posts()->whereHasMorph('post', \App\Shoutout::class);
    }

    public function textPosts()
    {
        return $this->posts()->where('post_type', '=', TextPost::class);
    }

    public function lounge()
    {
        return $this->hasOne(Lounge::class);
    }

    public function publishable_subgroups_recursive()
    {
        return $this->subgroups()->where('publish_to_parent_feed', 1)->with('publishable_subgroups_recursive');
    }

    public function dashboard_subgroups_recursive()
    {
        return $this->subgroups()->where('should_display_dashboard', 1)->with('dashboard_subgroups_recursive');
    }

    public function subgroupsUserIsMemberOf(User $user)
    {
        return $this->subgroups()->whereIn('id', \DB::table('group_user')->select('group_id')->where('user_id', '=', $user->id));
    }

    public function getIsLoungeEnabledAttribute()
    {
        return $this->lounge()->where('is_enabled', '=', 1)->count();
    }

    // all events avaible to this group
    public function getAllEventsAttribute()
    {
        $subgroupIds = flattenGroupTrees($this->publishable_subgroups_recursive)->pluck('id')->merge(collect([$this->id]));
        return Event::where(function ($query) use ($subgroupIds) {
            return $query->whereIn('group_id', $subgroupIds)
                        ->orWhereHas('groups', function ($query) use ($subgroupIds) {
                            return $query->whereIn('group_id', $subgroupIds);
                        });
        })->orderBy('date', 'desc')->where('events.is_cancelled', 0)->get();
    }

    public function events()
    {
        return $this->belongsToMany(Event::class);
    }

    // all events this group's admins can edit
    public function ownedEvents()
    {
        return $this->hasMany(Event::class);
    }

    public function getUpcomingEventsAttribute()
    {
        //events where start date is greater than the start of today and where the end date is after now (or null)
        $eventIds = $this->events()->pluck('id')->merge($this->ownedEvents()->pluck('id'));

        $events = Event::whereIn('id', $eventIds)
            ->where('date', '>', \Carbon\Carbon::now(request()->user()->timezone)->tz('UTC')->startOfDay())
            ->where('end_date', '>', \Carbon\Carbon::now(request()->user()->timezone)->tz('UTC')->toDateTimeString())
            ->orderBy('date', 'asc')->get();
        
        if ($events->count() < 6)
            $events = $events->merge($this->events->merge($this->ownedEvents)->where('date', '>', \Carbon\Carbon::now(request()->user()->timezone)->endOfDay()->tz('UTC'))->sortBy('date')->take(6 - $events->count()));

        return $events->where('is_cancelled', 0);
    }

    public function folders()
    {
        return $this->hasMany(Folder::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function isUserAdmin($userId, $checkSuperAdminStatus = true)
    {
        if ($checkSuperAdminStatus) {
            if ($userId instanceof User)
                $user = $userId;
            else
                $user = User::where('id', '=', $userId)->first();
            if($user->is_admin)
                return true;
        }

        if($userId instanceof User)
            $userId = $user->id;

        if($user = $this->users()->where('id', $userId)->first())
            return $user->pivot->is_admin;

        else if ($this->parent)
            return $this->parent->isUserAdmin($userId);
        else
            return false;
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function discussions()
    {
        return $this->hasMany(DiscussionThread::class);
    }

    public function subgroups()
    {
        return $this->hasMany(Group::class, 'parent_group_id');
    }

    public function parent()
    {
        return $this->belongsTo(Group::class, 'parent_group_id');
    }

    public function logs()
    {
        return $this->morphMany(Log::class, 'related_model');
    }

    public function registration_page()
    {
        return $this->belongsTo(registrationPage::class, 'join_via_registration_page');
    }

    public function getReportedPostsAttribute()
    {
        $reportedPosts = $this->posts()->whereHas('reported', function ($query) {
            $query->whereNull('resolved_by');
        })->get();

        $discussionsWithReportedPosts = $this->discussions()->whereHas('posts', function ($query) {
            $query->whereHas('reported', function ($q) {
                $q->whereNull('resolved_by');
            });
        })->get();

        $reportedPosts = $reportedPosts->merge($discussionsWithReportedPosts);
        
        return $reportedPosts;
    }

    public function getResolvedPostsAttribute()
    {
        $resolvedPosts = $this->posts()
             ->whereHas('reported', function ($q) {
                $q->whereNotNull('resolved_by');
             })
            ->whereHasMorph('post', [
                \App\TextPost::class,
                \App\Shoutout::class,
                \App\Event::class,
                \App\ArticlePost::class,
            ])->get();

        $resolvedDiscussions = $this->posts()
            ->whereHasMorph('post', [\App\DiscussionThread::class], function ($q) {
                $q->whereHas('posts', function ($qq) {
                    $qq->whereHas('reported', function ($qqq) {
                        $qqq->whereNotNull('resolved_by');
                     });
                });
            })->get();

        $resolvedPosts = $resolvedPosts->merge($resolvedDiscussions);
        
        return $resolvedPosts;
    }

    public function getDisabledContentTypes()
    {
        $types = ['App\Ideation'];
        if(!$this->is_shoutouts_enabled)
            $types[] = 'App\Shoutout';
        if(!$this->is_files_enabled)
            $types[] = 'App\File';
        if(!$this->is_budgets_enabled)
            $types[] = 'App\Budget';
        if(!$this->is_events_enabled)
            $types[] = 'App\Event';
        if(!$this->is_posts_enabled)
            $types[] = 'App\TextPost';
        if(!$this->is_content_enabled)
            $types[] = 'App\ArticlePost';
        if(!$this->is_discussions_enabled)
            $types[] = 'App\DiscussionThread';

        return collect($types);
    }

    public function customMenuWithLocale($locale)
    {
        return localizedValue('custom_menu', $this->localization, $locale) ?: false;
    }

    public function getCustomMenuAttribute($value)
    {
        return localizedValue('custom_menu', $this->localization) ? json_encode(localizedValue('custom_menu', $this->localization)) : $value;
    }

    public function getHeaderBgImageUrlAttribute()
    {
        if($this->header_bg_image_path)
            return getS3Url($this->header_bg_image_path);

        return false;
    }
    
    public function getThumbnailImageUrlAttribute()
    {
        return getS3Url($this->thumbnail_image_path);
    }

    public function doesUserHaveAccess($userId)
    {
        if(!$this->is_private)
            return true;
        
        return $this->users()->where('user_id', $userId)->count();
    }

    public function isUserMember($userId)
    {
        return $this->users()->where('user_id', $userId)->exists();
    }

    public function hasAccessableSubgroups($userId)
    {
        foreach($this->subgroups as $subgroup)
        {
            if($subgroup->doesUserHaveAccess($userId))
                return true;
        }
        return false;
    }

    public function getBannerCtaUsersAttribute()
    {
        if($this->banner_cta_url && $this->banner_cta_url != 'null')
            return ['users' => json_decode($this->banner_cta_url)];
        else
            return ['group' => $this->id];
    }

    public function getPinnedPostAttribute()
    {
        if($this->pinned_post_id)
            return Post::find($this->pinned_post_id);
        else
            return false;
    }
  
    public function getViewableGroupIdsAttribute()
    {
        $groups = flattenGroupTrees($this->publishable_subgroups_recursive)->pluck('id');
        $groups[] = $this->id;

        return $groups->toArray();
    }

    public function getDashboardSubgroupsAttribute()
    {
        $groups = collect([]);
        foreach($this->subgroups as $subgroup)
        {
            if($subgroup->should_display_dashboard)
            {
                $groups->push($subgroup);
                $groups->merge($subgroup->dashboard_subgroups);
            }
            else
                $groups->merge($subgroup->dashboard_subgroups);
        }

        return $groups;
    }

    public function getMembersPageAttribute()
    {
        if(!$this->members_page_name)
            return 'Members';
        else
            return $this->members_page_name;
    }

    public function getHomePageAttribute()
    {
        if(!$this->home_page_name)
            return 'Group Home';
        else
            return $this->home_page_name;
    }

    public function getPostsPageAttribute()
    {
        if(!$this->posts_page_name)
            return 'Posts';
        else
            return $this->posts_page_name;
    }

    public function getContentPageAttribute()
    {
        if(!$this->content_page_name)
            return 'Content';
        else
            return $this->content_page_name;
    }

    public function getCalendarPageAttribute()
    {
        if(!$this->calendar_page_name)
            return 'Calendar';
        else
            return $this->calendar_page_name;
    }

    public function getShoutoutsPageAttribute()
    {
        if(!$this->shoutouts_page_name)
            return 'Shoutouts';
        else
            return $this->shoutouts_page_name;
    }

    public function getDiscussionsPageAttribute()
    {
        if(!$this->discussions_page_name)
            return 'Discussions';
        else
            return $this->discussions_page_name;
    }

    public function getAncestorAttribute()
    {
        if($this->parent_group_id)
        {  
            $group = $this->parent;
            while(true)
            {
                if(!$group->parent_group_id)
                    return $group;
                else
                    $group = $group->parent;
            }
        }
        else
            return false;
    }

    public function scopeJoinable($query)
    {
        return $query->where(function ($query) {
            $query->whereNotNull('parent_group_id')->where('is_joinable', 1)->where('is_private', 0);
        })->orWhere(function ($query) {
            $query->whereNull('parent_group_id')->where('is_private', 0);
	   });
    }

    public function getJoinableSubgroupsAttribute($query)
    {
        return $this->subgroups()->joinable()->get();
    }

    //only to be called when the group is deleted
    public function notifications()
    {
        $shoutouts = \App\Notification::where('notifiable_type', 'App\Shoutout')->whereIn('notifiable_id', $this->shoutouts()->pluck('id'))->get();
        $textPosts = \App\Notification::where('notifiable_type', 'App\TextPost')->whereIn('notifiable_id', $this->textPosts()->pluck('id'))->get();
        $events = \App\Notification::where('notifiable_type', 'App\Event')->whereIn('notifiable_id', $this->events->pluck('id'))->get();
        $discussionThreads = \App\Notification::where('notifiable_type', 'App\DiscussionThread')->whereIn('notifiable_id', $this->discussions()->pluck('id'))->get();

        return $shoutouts->merge($textPosts->merge($events->merge($discussionThreads)));
    }

    public function getHasHomeImageAttribute()
    {
        $request = request();
        return ($this->header_bg_image_path || $this->is_virtual_room_enabled) || $request->is('*/content/add') || !$request->is('/groups/*');
    }

    public function addUsers($userIds)
    {
        if($this->parent)
            $this->parent->addUsers($userIds);

        return $this->users()->syncWithoutDetaching($userIds);
    }

    public function addUser($userId, $isAdmin = false)
    {
        $this->users()->syncWithoutDetaching([$userId => ['is_admin' => $isAdmin]]);
        if($this->parent)
            $this->parent->addUser($userId);
    }

    public function isGroupAdmin($userId)
    {
        return $this->admins()->where('user_id', $userId)->exists();
    }

    public function attributeWithLocale($attribute, $locale)
    {
        if(isset($this->localization[$locale]))
        {
            if(isset($this->localization[$locale][$attribute]))
            {
                return $this->localization[$locale][$attribute];
            }
        }
        return null;
    }

    public function getSearchResult(): SearchResult
    {
        $url = '/groups/' . $this->slug;
     
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

    public function getSubgroupsPageNameAttribute($value)
    {
        return localizedValue('subgroups_page_name', $this->localization) ?: $value;
    }

    public function getBannerCtaTitleAttribute($value)
    {
        return localizedValue('banner_cta_title', $this->localization) ?: $value;
    }

    public function getBannerCtaParagraphAttribute($value)
    {
        return localizedValue('banner_cta_paragraph', $this->localization) ?: $value;
    }

    public function getBannerCtaButtonAttribute($value)
    {
        return localizedValue('banner_cta_button', $this->localization) ?: $value;
    }

    public function getBannerCtaUrlAttribute($value)
    {
        return localizedValue('banner_cta_url', $this->localization) ?: $value;
    }

    public function getHomePageNameAttribute($value)
    {
        return localizedValue('home_page_name', $this->localization) ?: $value;
    }

    public function getPostsPageNameAttribute($value)
    {
        return localizedValue('posts_page_name', $this->localization) ?: $value;
    }

    public function getContentPageNameAttribute($value)
    {
        return localizedValue('content_page_name', $this->localization) ?: $value;
    }

    public function getCalendarPageNameAttribute($value)
    {
        return localizedValue('calendar_page_name', $this->localization) ?: $value;
    }

    public function getShoutoutsPageNameAttribute($value)
    {
        return localizedValue('shoutouts_page_name', $this->localization) ?: $value;
    }

    public function getDiscussionsPageNameAttribute($value)
    {
        return localizedValue('discussions_page_name', $this->localization) ?: $value;
    }

    public function getFilesAliasAttribute($value)
    {
        return localizedValue('files_alias', $this->localization) ?: ($value ?: 'Files');
    }

    public function getMembersPageNameAttribute($value)
    {
        return localizedValue('members_page_name', $this->localization) ?: $value;
    }

    public function getDashboardHeaderAttribute($value)
    {
        return localizedValue('dashboard_header', $this->localization) ?: ($value ? $value : 'Files');
    }

    public function scopeSlug($query, $slug)
    {
        return $query->where('slug', $slug)->first();
    }

    public function getIsChatRoomEnabledAttribute()
    {
        return $this->chatRoom()->exists() && $this->chatRoom->is_enabled;
    }

    public function movePostUp($postId)
    {
        $this->resetOrderedPosts();
        $fiftyOrderedPostIds = collect($this->ordered_post_ids);

        if(!$fiftyOrderedPostIds->contains($postId))
        {
            $arr = $fiftyOrderedPostIds->toArray();
            $lastIndex = count($arr) - 1;
            $arr[$lastIndex] = (int) $postId;

            $this->update([
                'post_order' => $arr,
                'post_order_last_set_at' => Carbon::now(),
            ]);
        }
        else
        {
            $arr = $fiftyOrderedPostIds->toArray();
            $indexOfPost = array_search($postId, $arr);
            if($indexOfPost == 0)
                return;

            $postToMoveDown = $arr[$indexOfPost - 1];
            $arr[$indexOfPost - 1] = (int) $postId;
            $arr[$indexOfPost] = $postToMoveDown;

            $this->update([
                'post_order' => $arr,
                'post_order_last_set_at' => Carbon::now(),
            ]);
        }
    }

    public function movePostDown($postId)
    {
        $this->resetOrderedPosts();
        $fiftyOrderedPostIds = collect($this->ordered_post_ids);

        if(!$fiftyOrderedPostIds->contains($postId))
        {
            return;
        }
        else
        {
            $arr = $fiftyOrderedPostIds->toArray();
            $indexOfPost = array_search($postId, $arr);

            if(!array_key_exists($indexOfPost + 1, $arr))
                return;
            $postToMoveUp = $arr[$indexOfPost + 1];
            $arr[$indexOfPost + 1] = (int) $postId;
            $arr[$indexOfPost] = $postToMoveUp;

            $this->update([
                'post_order' => $arr,
                'post_order_last_set_at' => Carbon::now(),
            ]);
        }
    }

    public function canUserPostAnything($user)
    {
        return ($this->is_posts_enabled || $this->is_events_enabled || $this->is_shoutouts_enabled || $this->is_content_enabled || $this->is_discussions_enabled)
        && 
        ($this->isUserAdmin($user->id) || ($this->can_users_post_events || $this->can_users_post_text || $this->can_users_post_content || $this->can_users_post_shoutouts || $this->can_users_post_discussions));
    }
    
}

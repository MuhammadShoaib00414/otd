<?php

namespace App\Http\Controllers;

use App\Post;
use App\User;
use App\Group;
use App\Lounge;
use App\Sequence;
use App\ClickArea;
use App\VideoRoom;
use Carbon\Carbon;
use App\VirtualRoom;
use App\SequenceUser;
use App\MessageThread;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class GroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('group')->except(['index', 'uploadImage', 'joinWithCode', 'register', 'postRegister']);
    }

    public function index(Request $request)
    {
        return view('groups.index')->with([
            'groups' => $request->user()->groups()->distinct()->whereNull('parent_group_id')->orderBy('name', 'asc')->get(),
        ]);
    }

    public function register(Request $request, $slug)
    {
        $group = Group::where('slug', $slug)->first();
        return view('groups.register')->with([
            'group' => $group,
            'page' => $group->registration_page,
        ]);
    }

    public function postRegister(Request $request, $slug)
    {
        $group = Group::where('slug', $slug)->first();

    }

    public function show(Request $request, $slug)
    {
       
        
        return redirect('/spa/#/groups/'.$slug);

        $group = Group::where('slug', '=', $slug)->first();
      
        if(!$group)
            return redirect('/home');

        // If a group is private, only allow access to members.
        if ($group->is_private && $group->allUsers()->where('users.id', '=', $request->user()->id)->count() == 0)
            return back();



        $ordered_post_ids = $group->ordered_post_ids;
   
        $posts = Post::with(['post' => function ($morphTo) {
                     $morphTo->morphWith([
                        \App\TextPost::class => [],
                        \App\Event::class => [],
                        \App\Shoutout::class => [],
                        \App\ArticlePost::class => [],
                    ]);
                }])
                ->where('posts.is_enabled', 1)
                ->groupPosts($group->viewable_group_ids)
                ->where('posts.post_at', '<=', Carbon::now()->toDateTimeString())
                ->orderBy('posts.post_at', 'desc')
                ->whereNotIn('posts.post_type', $group->getDisabledContentTypes());
// dd( $group->getDisabledContentTypes());
        if($ordered_post_ids)
        {
            $posts = $posts->whereIn('posts.id', $ordered_post_ids)
                        ->orderByRaw("FIELD(id, ".implode(',', $ordered_post_ids).")");
        }

        if($group->pinned_post_id)
            $posts->where('posts.id', '!=', $group->pinned_post_id);

        $posts = $posts->paginate(7);

        $pinned_post = $group->pinned_post;

        $twoWeekOldArticles = Post::where('posts.post_at', '<=', \DB::raw('DATE_SUB(curdate(), INTERVAL 2 WEEK)'))
                                 ->groupPosts($group->viewable_group_ids)
                                 ->where('post_type', '=', 'App\ArticlePost')
                                 ->whereHasMorph('post', [\App\ArticlePost::class])
                                 ->orderBy('posts.post_at', 'desc')
                                 ->simplePaginate(10);

        $flagged = \App\DiscussionPost::whereHas('thread', function ($query) use ($group) {
            $query->where('group_id', '=', $group->id);
        })->whereNotNull('reported_by')
           ->get();

        $agent = new \Jenssegers\Agent\Agent;

        if($group->is_virtual_room_enabled && $group->virtualRoom && $group->virtualRoom->image_path && ($agent->isDesktop() || !$group->mobile_virtual_room))
            $room = $group->virtual_room;
        else if($group->is_virtual_room_enabled && $group->mobile_virtual_room && $group->mobile_virtual_room->image_path && $agent->isMobile())
            $room = $group->mobile_virtual_room;
        else
            $room = false;

        if ($group->is_sequence_visible_on_group_dashboard && $group->sequence) {
            $sequenceUser = SequenceUser::where([
                'user_id' => $request->user()->id,
                'sequence_id' => $group->sequence->id,
            ])->first();

            if (!$sequenceUser) {
                $sequenceUser = SequenceUser::make([
                    'user_id' => $request->user()->id,
                    'sequence_id' => $group->sequence->id,
                    'last_completed_module_id' => 0,
                ]);
            }

            //only completed modules and the first incomplete module should be available
            $modules = $group->sequence->modules()->orderBy('order_key', 'asc')->get();
            $nextShouldBeUnavailable = false;
            $user = $request->user();
            $lastAvailableIndex = 0;
            foreach ($modules as $key => $module) {
                if($module->hasUserCompleted($user) && !$nextShouldBeUnavailable)
                {
                    $module->is_available = true;
                    $lastAvailableIndex++;
                }
                elseif($key == 0)
                {
                    $module->is_available = true;
                    $nextShouldBeUnavailable = true;
                }
                elseif(!$nextShouldBeUnavailable)
                {
                    $module->is_available = true;
                    $nextShouldBeUnavailable = true;
                }
                else
                    $module->is_available = false;
            }
        } else {
            $modules = [];
            $sequenceUser = optional(null);
        }

        if(isset($lastAvailableIndex) && $lastAvailableIndex == count($modules))
            $lastAvailableIndex--;

        $request->user()->logs()->create([
            'action' => 'viewed this group',
            'related_model_type' => 'App\Group',
            'related_model_id' => $group->id,
        ]);

        return view('groups.show')->with([
            'group' => $group,
            'posts' => $posts,
            'flaggedDiscussionPosts' => $flagged,
            'twoWeekOldArticles' => $twoWeekOldArticles,
            'pinned_post' => $pinned_post,
            'room' => $room,
            'modules' => $modules,
            'sequenceUser' => $sequenceUser,
            'last_available_module_index' => isset($lastAvailableIndex) ? $lastAvailableIndex : null,
        ]);
    }

    public function edit(Request $request, $slug)
    {
    
        return view('groups.edit')->with([
            'group' => Group::where('slug', $slug)->first(),
        ]);
    }

    public function update(Request $request, $slug)
    {
        $request->validate([
            'header_bg_image' => 'file|max:51200',
            'thumbnail_image' => 'file|max:51200',
        ]);
        $group = Group::where('slug', $slug)->first();

        $group->update($request->except(['_method', '_token', 'tab', 'custom_menu', 'users']));

        if($request->tab == "custom_menus") {
            $custom_menu = json_decode($request->custom_menu);
            foreach($custom_menu->groups as &$menu_group) {
                foreach($menu_group->links as &$link) {
                    if(!preg_match('%http%', $link->url))
                        $link->url = 'http://'.$link->url;
                }
            }

            $group->update(['custom_menu' => json_encode(['groups' => $custom_menu->groups])]);

            foreach($custom_menu->spanishGroups as &$spanish_group)
            {
                foreach($spanish_group->links as &$spanish_link)
                {
                    if(!preg_match('%http%', $spanish_link->url))
                        $spanish_link->url = 'http://'.$spanish_link->url;
                }
            }

            $localization = $group->localization;
            $localization['es']['custom_menu']['groups'] = (array) $custom_menu->spanishGroups;
            $group->update(['localization' => $localization]);

            $request->users = $request->users ? json_encode($request->users) : '';
        }

        if($request->tab == 'general' && $group->can_group_admins_manage_virtual_room) {
            $group->update([
                'is_virtual_room_enabled' => ($request->has('is_virtual_room_enabled')) ? 1 : 0,
            ]);
        }

        if ($request->has('header_bg_image') || $request->has('header_bg_image_remove')) {
            if(!$request->has('header_bg_image_remove'))
            {
                $path = $request->file('header_bg_image')->store('groups', 's3', ['visibility' => 'public', 'Expires' => Carbon::now()->addYears(1) ]);
                // optimizeImage(public_path() . '/uploads/' . $path, 1800);
            }
            else
                $path = '';
            $group->update([
                'header_bg_image_path' => $path,
            ]);
        }
        if ($request->has('thumbnail_image') || $request->has('thumbnail_image_remove')) {
            if(!$request->has('thumbnail_image_remove'))
            {
                $path = $request->file('thumbnail_image')->store('groups', 's3');
                // optimizeImage(public_path() . '/uploads/' . $path, 600);
            }
            else
                $path = '';
            $group->update([
                'thumbnail_image_path' => $path,
            ]);
        }

        if ($request->has('users')) {
            $group->update([
                'banner_cta_url' => $request->users,
            ]);
        }

        if($group->can_ga_toggle_content_types && $request->has('is_files_enabled'))
        {
            $group->update([
                'is_files_enabled' => $request->input('is_files_enabled'),
                'is_budgets_enabled' => $request->input('is_budgets_enabled'),
                'is_discussions_enabled' => $request->input('is_discussions_enabled'),
                'is_shoutouts_enabled' => $request->input('is_shoutouts_enabled'),
                'is_posts_enabled' => $request->input('is_posts_enabled'),
                'is_events_enabled' => $request->input('is_events_enabled'),
                'is_content_enabled' => $request->input('is_content_enabled'),
            ]);
        }

        if($request->has('is_chat_room_enabled'))
        {
            if ($request->is_chat_room_enabled == "true") {
                $group->chatRoom()->updateOrCreate([],[
                    'is_enabled' => 1,
                    'start_at' => ($request->live_chat_start_date != '' && $request->live_chat_start_time != '') ? Carbon::parse($request->live_chat_start_date . ' ' . $request->live_chat_start_time, $request->user()->timezone)->tz('UTC')->toDateTimeString() : null,
                    'end_at' => ($request->live_chat_end_date != '' && $request->live_chat_end_time != '') ? Carbon::parse($request->live_chat_end_date . ' ' . $request->live_chat_end_time, $request->user()->timezone)->tz('UTC')->toDateTimeString() : null,
                ]);
            } else {
                $group->chatRoom()->update(['is_enabled' => 0, 'start_at' => null, 'end_at' => null]);
            }
        }

        if($request->has('create_zoom_meeting') && is_zoom_enabled() && !$group->zoom_meeting_id)
            $this->createZoomMeeting($group);

        return redirect('/groups/' . $slug . '/edit#' . $request->tab);
    }

    public function subgroupsIndex(Request $request, $slug)
    {
        $group = Group::where('slug', $slug)->first();

        // move the subgroups with order_key == null to the end of the list
        $subgroups = $group->subgroups()->orderBy('order_key', 'asc')->get();
        $subgroups = $subgroups->where('order_key', '!=', null)->merge($subgroups->where('order_key', '=', null));

        $request->user()->logs()->create([
            'action' => 'viewed subgroups',
            'related_model_type' => 'App\Group',
            'related_model_id' => $group->id,
        ]);

        return view('groups.subgroups.index')->with([
            'group' => $group,
            'subgroups' => $subgroups,
        ]);
    }

    public function uploadImage(Request $request)
    {
        $url = $request->file('file')->store('email-images');

        return response()->json(['url' => config('app.url') . "/uploads/" . $url ]);
    }

    public function editVirtualRoom($slug, Request $request)
    {
        $group = Group::where('slug', $slug)->first();
        if($request->has('isMobile') && $request->isMobile == true)
            $room = $group->mobile_virtual_room;
        else
            $room = $group->virtual_room;

        if ($room)
            $areas = $room->clickAreas;
        else
            $areas = collect([]);

        return view('groups.virtualroom.edit')->with([
            'group' => $group,
            'areas' => $areas->toJson(),
            'room' => $room,
        ]);
    }

    public function newRoom($slug, Request $request)
    {
        $group = Group::where('slug', '=', $slug)->first();
        $request->validate([
            'photo' => 'file|max:51200',
        ]);
        $path = $request->file('photo')->store('virtual-rooms', 's3');
        // optimizeImage(public_path() . '/uploads/' . $path, 1800);


        VirtualRoom::create([
            'group_id' => $group->id,
            'image_path' => $path,
            'is_mobile' => $request->has('is_mobile'),
        ]);

        return redirect('/groups/'.$slug.'/edit-virtual-room'.($request->has('is_mobile') ? '?isMobile=true' : ''));
    }

    public function saveAreas($slug, Request $request)
    {
        $group = Group::where('slug', '=', $slug)->first();
        $room = $request->has('is_mobile') ? $group->mobile_virtual_room : $group->virtual_room;
        $room->clickAreas()->delete();
        $virtualRoomId = $room->id;

        $areas = collect($request->click_areas);
        $areas = $areas->map(function ($area) use ($virtualRoomId) {
            ClickArea::create([
                'width' => $area['width'],
                'height' => $area['height'],
                'x_coor' => $area['left'],
                'y_coor' => $area['top'],
                'virtual_room_id' => $virtualRoomId,
                'target_url' => $area['url'],
                'a_target' => $area['target'],
            ]);
        });
        Cache::forget('dashboard-header-for-api');
        return 'success';
    }
    
            public function changeImage($slug, Request $request)
            {
                
                set_time_limit(0);
                $request->validate([
                    'photo' => 'file|max:51200',
                ]);
                
                $group = Group::where('slug', '=', $slug)->first();
                $file = request()->file('photo');
                $img_width = Image::make($file)->width();

                $imageName = $file->getClientOriginalName();
                $img = Image::make($file);
                $img->resize(1900, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                if($img_width > 1900){
                    $path = Storage::disk('s3')->put(
                        'virtual-rooms/' . $imageName,
                        $img->stream()->__toString(),
                    );
                }else{
                    $path = Storage::disk('s3')->put(
                        'virtual-rooms/' . $imageName,
                        $img->stream(),
                    ); 
                }
                
            
                $room = $request->has('is_mobile') ? $group->mobile_virtual_room : $group->virtual_room;
                $room->update([
                    'image_path' =>  'virtual-rooms/'.$request->file('photo')->getClientOriginalName(),
                ]);
                return redirect('/groups/'.$slug.'/edit-virtual-room'.($request->has('is_mobile') ? '?isMobile=true' : ''));
            }

  

    public function editLounge($slug, Request $request)
    {
        $group = Group::where('slug', $slug)->first();
        $room = $request->has('isMobile') ? $group->lounge->mobile_virtual_room : $group->lounge->virtual_room;

        if ($room)
            $areas = $room->clickAreas;
        else
            $areas = collect([]);

        return view('groups.lounge.edit')->with([
            'group' => $group,
            'areas' => $areas->toJson(),
            'room' => $room
        ]);
    }

    public function newLounge($slug, Request $request)
    {
        $group = Group::where('slug', '=', $slug)->first();
        $request->validate([
            'photo' => 'file|max:51200',
        ]);
        $path = $request->file('photo')->store('virtual-rooms', 's3');
        // optimizeImage(public_path() . '/uploads/' . $path, 1800);

        $room = VirtualRoom::create([
            'group_id' => $group->id,
            'image_path' => $path,
            'is_mobile' => $request->has('is_mobile'),
        ]);

        if($request->has('is_mobile'))
        {
            $group->lounge()->update([
                'mobile_virtual_room_id' => $room->id,
            ]);
        }
        else
        {
            $group->lounge()->update([
                'virtual_room_id' => $room->id,
            ]);
        }

        return redirect('/groups/'.$slug.'/edit-lounge'.($request->has('is_mobile') ? '?isMobile=true' : ''));
    }

    public function saveLoungeAreas($slug, Request $request)
    {
        $group = Group::where('slug', '=', $slug)->first();
        $room = $request->has('is_mobile') ? $group->lounge->mobile_virtual_room : $group->lounge->virtual_room;
        $room->clickAreas()->delete();
        $virtualRoomId = $room->id;

        $areas = collect($request->click_areas);
        $areas = $areas->map(function ($area) use ($virtualRoomId) {
            ClickArea::create([
                'width' => $area['width'],
                'height' => $area['height'],
                'x_coor' => $area['left'],
                'y_coor' => $area['top'],
                'virtual_room_id' => $virtualRoomId,
                'target_url' => $area['url'],
                'a_target' => $area['target'],
            ]);
        });

        return 'success';
    }

    public function changeLoungeImage($slug, Request $request)
    {
        $request->validate([
            'photo' => 'file|max:51200',
        ]);
        $group = Group::where('slug', '=', $slug)->first();
        $path = $request->file('photo')->store('virtual-rooms', 's3');
        $room = $request->has('is_mobile') ? $group->lounge->mobile_virtual_room : $group->lounge->virtual_room;
        # optimizeImage(public_path() . '/uploads/' . $path, 1800);
        $room->update([
            'image_path' => $path,
        ]);

        return redirect('/groups/'.$slug.'/edit-lounge'.($request->has('is_mobile') ? '?isMobile=true' : ''));
    }

    public function logClickedSubgroup(Group $group, Group $subgroup, Request $request)
    {
        $request->user()->logs()->create([
            'action' => 'clicked subgroup',
            'related_model_type' => 'App\Group',
            'related_model_id' => $group->id,
            'secondary_related_model_type' => 'App\Group',
            'secondary_related_model_id' => $subgroup->id,
        ]);

        return redirect('/groups/' . $subgroup->slug); 
    }

    public function createSequence(Group $group, Request $request)
    {
        $sequence = Sequence::create(['name' => 'Learning Modules']);
        $group->update(['sequence_id' => $sequence->id]);

        return redirect('/groups/'.$group->slug.'/edit#sequence');
    }

    public function updateSequence(Group $group, Request $request)
    {
        $changesArray = [ 'name' => $request->name ];

        if ($request->has('completed_thumbnail_image_path')) {
            $changesArray['completed_thumbnail_image_path'] = $request->file('completed_thumbnail_image_path')->store('module-images', 's3');
            // optimizeImage(public_path() . '/uploads/' . $changesArray['completed_thumbnail_image_path'], 1000);
        }
        $changesArray['is_completion_shoutouts_enabled'] = ($request->has('is_completion_shoutouts_enabled')) ? 1 : 0;
        $changesArray['completed_badge_id'] = ($request->has('completed_badge_id') && $request->completed_badge_id != 'null') ? $request->completed_badge_id : null;

        $group->sequence()->update($changesArray);
        $group->update([ 'is_sequence_visible_on_group_dashboard' => $request->has('is_sequence_visible_on_group_dashboard') ? 1 : 0 ]);

        return redirect('/groups/'.$group->slug.'/edit#sequence');
    }

    public function joinWithCode(Request $request)
    {
        $group = Group::where('join_code', '=', $request->code)->first();

        if(!$group)
            return redirect()->back()->withErrors(['msg' => 'Invalid code.']);

        $user = $request->user();
        $user->groups()->syncWithoutDetaching($group);
        //join parent groups if not a member
        if($group->parent)
        {
            $groupToJoin = $group->parent;
            while(isset($groupToJoin))
            {
                $user->groups()->syncWithoutDetaching($groupToJoin->id);
                if(!$groupToJoin->parent_group_id)
                    break;
                $groupToJoin = $groupToJoin->parent;
            }
        }
        $user->logs()->create([
            'action' => 'joined group via specialized access code: ' . $request->access_code,
        ]);
        if ($group->is_welcome_message_enabled && $group->welcome_message_sending_user_id && $group->welcome_message) {
            $sendingUser = User::find($group->welcome_message_sending_user_id);
            $participants = [
                $sendingUser,
                $user,
            ];
            $thread = MessageThread::create();
            $thread->participants()->saveMany($participants);
            $message = $thread->messages()->create([
                'sending_user_id' => $sendingUser->id,
                'message' => $group->welcome_message,
            ]);
            event(new MessageSent($sendingUser, $thread, $message));
        }

        return back()->with(['success' => 'Joined ' . $group->name . ' successfully.']);
    }

    public function createZoomMeeting($group)
    {
        $zoom_user_id = $group->zoom_user_id;
        $path = 'users/'.$zoom_user_id.'/meetings';

        $response = $this->zoomPost($path, [
            'topic' => $group->name,
            'type' => 2, // scheduled meeting type
            'start_time' => $this->toZoomTimeFormat(Carbon::now()),
            'duration' => 30,
            'agenda' => '',
            'settings' => [
                'host_video' => true,
                'participant_video' => false,
                'waiting_room' => true,
                'join_before_host' => true,
                'participant_video' => true,
                'show_share_button' => true,
            ]
        ]);

        $response = json_decode($response->body(), true);

        $group->update([
            'zoom_meeting_id' => $response['id'],
            'zoom_meeting_password' => $response['password'],
        ]);
    }

}

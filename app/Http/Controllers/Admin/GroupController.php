<?php

namespace App\Http\Controllers\Admin;

use App\Group;
use App\Http\Controllers\Controller;
use App\Lounge;
use App\Notification;
use App\RegistrationPage;
use App\Setting;
use App\Traits\ZoomTrait;
use App\User;
use App\VideoRoom;
use App\VirtualRoom;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    use ZoomTrait;

    public function index(Request $request)
    {
        if ($request->has('deleted'))
            $groups = Group::whereNotNull('deleted_at')->withTrashed()->get();
        else
            $groups = Group::whereNull('parent_group_id')->get();

        return view('admin.groups.index')->with([
            'groups' => $groups,
        ]);
    }

    public function show($id, Request $request)
    {
        $fromDB = \App\Log::select(DB::raw('count(*) as count, DATE_FORMAT(created_at, "%b %e") as date'))
                             ->groupBy('date')
                             ->where('created_at', '>', Carbon::parse('13 days ago')->toDateTimeString())
                             ->whereIn('user_id', function ($query) use ($id) {
                                $query->select('user_id')
                                      ->from('group_user')
                                      ->where('group_id', '=', $id);
                             })
                             ->pluck('count', 'date');

        $dates = collect();
        foreach( range( 0, -14 ) AS $i ) {
            $date = Carbon::now()->addDays( $i )->format('M j');
            $dates->put($date, 0);
        }
        $dates = $dates->merge($fromDB)->reverse();

        $leaderboard = \App\AwardedPoint::with('user')
                                        ->addSelect(DB::raw("SUM(points) as total, user_id"))
                                        ->groupBy('user_id')
                                        ->orderBy('total', 'desc')
                                        ->whereIn('user_id', function ($query) use ($id) {
                                            $query->select('user_id')
                                                  ->from('group_user')
                                                  ->where('group_id', '=', $id);
                                         })
                                        ->limit(10)
                                        ->where('created_at', '>', Carbon::parse('-30 days')->toDateTimeString())->get();

        return view('admin.groups.show')->with([
            'group' => Group::withTrashed()->find($id),
            'activity' => $dates,
            'leaderboard' => $leaderboard,
        ]);
    }

    public function create()
    {
        $groups = Group::orderBy('name', 'asc')->get();

        if(isset($_GET['parent']))
        {
            $parent = Group::find($_GET['parent']);
            if($parent)
                $groups = $groups->merge(collect([$parent]));
        }

        return view('admin.groups.create')->with([
            'groups' => $groups,
            'users' => User::orderBy('name', 'asc')->where('is_enabled', 1)->get(),
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required',
            'group_admin' => 'required|int',
            'parent_group_id' => 'nullable|int',
        ]);

        if(Group::where('slug', $request->slug)->count())
            return redirect()->back()->withErrors(['msg' => 'This vanity URL is already taken.']);

        $group = Group::create([
            'name' => $request->input('name'),
            'slug' => $request->input('slug'),
            'parent_group_id' => ($request->has('parent_group_id')) ? $request->parent_group_id : null,
            'is_private' => $request->input('is_private'),
            'should_display_dashboard' => $request->has('should_display_dashboard') ? $request->should_display_dashboard : 0,
            'dashboard_header' => 'MY GROUPS',
            'banner_cta_title' => 'Have a question?',
            'banner_cta_paragraph' => 'Just ask!',
            'banner_cta_button' => 'Message an admin',
        ]);
        $group->users()->attach($request->group_admin, ['is_admin' => 1]);

        $action = \App\Setting::where('name', 'group_admins')->first()->value;

        if($action == 'hide')
            User::find($request->group_admin)->update(['is_hidden' => 1]);
        else if($action == 'show')
            User::find($request->group_admin)->update(['is_hidden' => 0]);

        return redirect('/admin/groups/' . $group->id);
    }
    
    public function edit($id, Request $request)
    {
        $group = Group::withTrashed()->find($id);
        $groups = Group::orderBy('name', 'asc')->where('id', '!=', $id)->get();
        $existingHeaders = DB::table('groups')->groupBy('dashboard_header')->select('dashboard_header')->get();
        if($group->parent()->count() && !$group->parent()->pluck('deleted_at')->count())
            $groups = $groups->merge(collect([$group->parent()->withTrashed()->get()]));

        return view('admin.groups.edit')->with([
            'group' => $group,
            'groups' => $groups,
            'existingHeaders' => $existingHeaders,
            'pages' => RegistrationPage::all(),
        ]);
    }
    
    public function update($id, Request $request)
    {
        $request->validate([
            'header_bg_image' => 'file|max:51200',
            'thumbnail_image' => 'file|max:51200',
        ]);
        $group = Group::withTrashed()->find($id);

        if(Group::where('slug', $request->input('slug'))->where('id', '!=', $id)->count())
            return redirect()->back()->withErrors(['msg' => 'This vanity URL is already taken.']);

        $group->update([
            'name' => $request->input('name'),
            'slug' => $request->input('slug'),
            'should_display_dashboard' => $request->input('should_display_dashboard'),
            'parent_group_id' => $request->input('parent_group_id'),
            'is_files_enabled' => $request->input('is_files_enabled'),
            'is_budgets_enabled' => $request->input('is_budgets_enabled'),
            'is_discussions_enabled' => $request->input('is_discussions_enabled'),
            'is_shoutouts_enabled' => $request->input('is_shoutouts_enabled'),
            'is_posts_enabled' => $request->input('is_posts_enabled'),
            'is_events_enabled' => $request->input('is_events_enabled'),
            'is_content_enabled' => $request->input('is_content_enabled'),
            'files_alias' => $request->input('files_alias'),
            'subgroups_page_name' => $request->input('subgroups_page_name'),
            'publish_to_parent_feed' => $request->has('publish_to_parent_feed') ? $request->publish_to_parent_feed : 0,
            'is_private' => $request->input('is_private'),
            'members_page_name' => $request->input('members_page_name'),
            'home_page_name' => $request->input('home_page_name'),
            'posts_page_name' => $request->input('posts_page_name'),
            'content_page_name' => $request->input('content_page_name'),
            'calendar_page_name' => $request->input('calendar_page_name'),
            'shoutouts_page_name' => $request->input('shoutouts_page_name'),
            'discussions_page_name' => $request->input('discussions_page_name'),
            'is_virtual_room_enabled' => ($request->has('is_virtual_room_enabled')) ? 1 : 0,
            'is_email_campaigns_enabled' => $request->input('is_email_campaigns_enabled'),
            'dashboard_header' => ($request->has('dashboard_header_custom') && !empty($request->dashboard_header_custom)) ? $request->dashboard_header_custom : strtoupper($request->input('dashboard_header')),
            'can_ga_toggle_content_types' => $request->input('can_ga_toggle_content_types'),
            'can_users_post_text' => $request->input('can_users_post_text'),
            'can_users_post_shoutouts' => $request->input('can_users_post_shoutouts'),
            'can_users_post_events' => $request->input('can_users_post_events'),
            'can_users_post_content' => $request->input('can_users_post_content'),
            'can_users_upload_files' => $request->input('can_users_upload_files'),
            'can_users_invite' => $request->input('can_users_invite'),
            'can_users_message_group' => $request->input('can_users_message_group'),
            'is_joinable' => $request->input('is_joinable'),
            'can_ga_set_live_chat' => $request->input('can_ga_set_live_chat'),
            'is_reporting_enabled' => $request->has('is_reporting_enabled') ? 1 : 0,
            'is_reporting_user_data_enabled' => $request->has('is_reporting_user_data_enabled') ? 1 : 0,
            'can_group_admins_manage_virtual_room' => $request->has('can_group_admins_manage_virtual_room') ? 1 : 0,
            'can_group_admins_invite_other_groups_to_events' => $request->has('can_group_admins_invite_other_groups_to_events') ? 1 : 0,
            'can_group_admins_schedule_posts' => $request->has('can_group_admins_schedule_posts') ? 1 : 0,
            'localization' => $request->localization,
            'enable_video_conference_in_lounge' => $request->has('enable_video_conference_in_lounge') ? 1 : 0,
            'publish_to_dashboard_feed' => $request->input('publish_to_dashboard_feed'),
            'can_ga_order_posts' => $request->has('can_ga_order_posts'),
            'should_live_chat_display_below_header_image' => $request->has('should_live_chat_display_below_header_image'),
            'join_code' => $request->join_code,
            'is_welcome_message_enabled' => $request->has('is_welcome_message_enabled'),
            'welcome_message_sending_user_id' => $request->welcome_message_sending_user_id,
            'welcome_message' => $request->welcome_message,
            'is_sequence_enabled' => $request->has('is_sequence_enabled'),
            'can_users_post_discussions' => $request->input('can_users_post_discussions'),
            'join_via_registration_page' => $request->join_via_registration_page,
        ]);
        if ($request->user()->is_super_admin && $request->has('embed_code'))
            $group->update(['embed_code' => $request->embed_code ]);

        if ($request->has('is_lounge_enabled')) {
            $lounge = $group->lounge;
            if ($lounge) {
                $lounge->update(['is_enabled' => 1]);
            } else {
                $virtualRoom = VirtualRoom::create([]);
                $lounge = $group->lounge()->create([
                    'name' => 'Networking Lounge',
                    'virtual_room_id' => $virtualRoom->id,
                ]);
            }
            $lounge->chatRoom()->updateOrCreate([],[
                'is_enabled' => 1,
                'start_at' => null,
                'end_at' => null,
            ]);
        } else {
            $lounge = $group->lounge;
            if ($lounge)
                $lounge->update(['is_enabled' => 0]);
        }

        if ($request->is_video_room_enabled && !$group->zoom_meeting_id)
            $this->createZoomMeeting($group);
        elseif($group->zoom_meeting_id)
        {
            $group->update([
                'zoom_meeting_id' => '',
                'zoom_meeting_password' => '',
            ]);
        }

        if ($request->has('header_bg_image') || $request->has('header_bg_image_remove')) {
            $group->update([
                'header_bg_image_path' => $request->has('header_bg_image_remove') ? '' : $request->file('header_bg_image')->store('groups', 's3'),
            ]);
        }
        if ($request->has('thumbnail_image') || $request->has('thumbnail_image_remove')) {
            $group->update([
                'thumbnail_image_path' => $request->has('thumbnail_image_remove') ? '' : $request->file('thumbnail_image')->store('groups', 's3'),
            ]);
        }
        if($request->has('is_public'))
            $group->update(['is_public' => $request->input('is_public')]);
        if ($request->is_chat_room_enabled == "true") {
            $group->chatRoom()->updateOrCreate([],[
                'is_enabled' => 1,
                'start_at' => ($request->live_chat_start_date != '' && $request->live_chat_start_time != '') ? Carbon::parse($request->live_chat_start_date . ' ' . $request->live_chat_start_time, $request->user()->timezone)->tz('UTC')->toDateTimeString() : null,
                'end_at' => ($request->live_chat_end_date != '' && $request->live_chat_end_time != '') ? Carbon::parse($request->live_chat_end_date . ' ' . $request->live_chat_end_time, $request->user()->timezone)->tz('UTC')->toDateTimeString() : null,
            ]);
        } else {
            $group->chatRoom()->update(['is_enabled' => 0, 'start_at' => null, 'end_at' => null]);
        }

        if($request->has('enable_zoom'))
        {
            $zoom_meeting_data = $this->parseZoomLink($request->zoom_meeting_link);
            if(!$zoom_meeting_data)
                return redirect()->back()->withErrors('msg', 'This zoom invite link is invalid.');

            $group->update($zoom_meeting_data);
        }
        else if(!$request->has('enable_zoom') && $group->zoom_invite_link)
            $group->update(['zoom_meeting_id' => null, 'zoom_meeting_password' => null, 'zoom_invite_link' => null]);

        return redirect("/admin/groups/".$group->id."/settings");
    }

    public function parseZoomLink($link)
    {
        if(!array_key_exists(4, explode('/', $link)))
            return false;

        $exploded_link = explode('/', $link);
        $relevant_information = $exploded_link[array_key_last($exploded_link)];

        $data = explode('?pwd=', $relevant_information);

        return [
            'zoom_meeting_id' => $data[0],
            'zoom_meeting_password' => array_key_exists(1, $data) ? $data[1] : '',
            'zoom_invite_link' => $link,
        ];
    }

    public function users($id, Request $request)
    {
        $group = Group::withTrashed()->find($id);

        if ($request->has('q')) {
            $query = $request->input('q');
            $users = $group->users()->where(function ($eQuery) use ($query) {
                        $eQuery->where('name', 'LIKE', '%' . $query . '%')
                              ->orWhere('job_title', 'LIKE', '%' . $query . '%')
                              ->orWhere('company', 'LIKE', '%' . $query . '%')
                              ->orWhere('location', 'LIKE', '%' . $query . '%');
                    })->paginate(50);
        } else {
            $users = $group->users()->orderBy('name', 'asc')->paginate(50);
        }

        return view('admin.groups.users')->with([
            'group' => $group,
            'users' => $users,
        ]);
    }

    public function addAllUsers($id, Request $request)
    {
        $group = Group::withTrashed()->find($id);
        $emails = explode(',', str_replace(' ', '', $request->users));
        $users = User::whereIn('email', $emails)->get();
        $attached = $group->addUsers($users)['attached'];

        return redirect('/admin/groups/' . $id . '/users')->with([
            'success' => count($attached) . ' users added to group.',
        ]);
    }

    public function bulkAddUsers($id)
    {
        $group = Group::find($id);
        $users = User::visible()->whereNotIn('id', $group->users()->pluck('id'))->orderBy('name', 'asc')->get();
        $groups = Group::whereNull('parent_group_id')->where('id', '!=', $id)->with('subgroups')->orderBy('name', 'asc')->get();

        return view('admin.groups.bulkAdd')->with([
            'group' => $group,
            'otherGroups' => $groups,
            'users' => $users,
        ]);
    }

    public function updateBulkUsers($id, Request $request)
    {
        $group = Group::find($id);

        $oldCount = $group->users()->count();

        if($request->has('users'))
            $group->addUsers($request->users);

        if($request->has('groups'))
        {
            foreach($request->groups as $otherGroupId)
            {
                $otherGroup = Group::find($otherGroupId);
                $group->addUsers($otherGroup->users()->pluck('id'));
            }
        }

        $addedUsersCount = $group->users()->count() - $oldCount;

        return redirect('/admin/groups/'.$id.'/users')->with('success', $addedUsersCount . ' Users added.');
    }

    public function budgets($id, Request $request)
    {
        $group = Group::withTrashed()->find($id);

        return view('admin.groups.budgets')->with([
            'group' => $group,
        ]);
    }

    public function delete($id)
    {
        $group = Group::withTrashed()->find($id);
        $this->deleteSubgroupsRecursive($group);
        Notification::whereIn('id', $group->notifications()->pluck('id'))->delete();
        $group->delete();

        return redirect('/admin/groups');
    }

    public function deleteSubgroupsRecursive($group)
    {
        foreach($group->subgroups as $subgroup)
        {
            $this->deleteSubgroupsRecursive($subgroup);
            Notification::whereIn('id', $subgroup->notifications()->pluck('id'))->delete();
            $subgroup->delete();
        }
    }

    public function assign($groupId)
    {
        Group::withTrashed()->find($groupId)->users()->sync(User::all());

        return redirect('/admin/groups/'.$groupId.'/users');
    }

    public function bulkAddUsersToGroup(Request $request)
    {
        $group = Group::find($request->group_id);
        $group->addUsers($request->users);

        return redirect('/admin/groups/' . $group->id);
    }

    public function indexSort()
    {
        $groups = Group::whereNull('parent_group_id')->orderBy('dashboard_order_key')->get()->groupBy('dashboard_header');

        return view('admin.groups.sort')->with([
            'groupHeaders' => $groups,
        ]);
    }

    public function sort(Request $request)
    {
        $headerCount = 0;
        foreach($request->categories as $header => $groups)
        {
            $count = 0;
            foreach($groups as $groupId)
            {
                if($groupId)
                {
                    Group::where('id', $groupId)->update([
                        'dashboard_order_key' => $headerCount . '-' . $count,
                        'dashboard_header' => $header,
                    ]);
                }
                $count++;
            }
            $headerCount++;
        }

        return response(200);
    }

    public function bulkSettings()
    {
        return view('admin.groups.bulkSettings')->with([
            'groups' => Group::whereNull('parent_group_id')->orderBy('dashboard_order_key')->get(),
        ]);
    }

    public function updateBulkSettings(Request $request)
    {
        foreach($request->groups as $groupId => $data)
        {
            $group = Group::find($groupId);
            $group->update($data);

            if($data['is_lounge_enabled'] == 1)
            {
                $lounge = $group->lounge;
                if ($lounge) {
                    $lounge->update(['is_enabled' => 1]);
                } else {
                    $virtualRoom = VirtualRoom::create([]);
                    $lounge = $group->lounge()->create([
                        'name' => 'Networking Lounge',
                        'virtual_room_id' => $virtualRoom->id,
                    ]);
                }
                $lounge->chatRoom()->updateOrCreate([],[
                    'is_enabled' => 1,
                    'start_at' => null,
                    'end_at' => null,
                ]);
            } else {
                $lounge = $group->lounge;
                if ($lounge)
                    $lounge->update(['is_enabled' => 0]);
            }
            if (array_key_exists('is_chat_room_enabled', $data)) {
                $group->chatRoom()->updateOrCreate([],[
                    'is_enabled' => 1,
                    'start_at' => null,
                    'end_at' => null,
                ]);
            } else {
                $group->chatRoom()->update(['is_enabled' => 0, 'start_at' => null, 'end_at' => null]);
            }
        }

        return redirect('/admin/groups/bulk-settings')->with('success', 'Settings saved successfully.');
    }

    public function createZoomMeeting($group)
    {   
        $path = 'users/me/meetings';
        $response = $this->zoomPost($path, [
            'topic' => $group->title,
            'type' => 2, // scheduled meeting type
            'start_time' => $this->toZoomTimeFormat(Carbon::now()),
            'duration' => 30,
            'agenda' => $group->description,
            'passcode' => '1234',
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

    public function configuration()
    {
        return view('admin.groups.configuration')->with([
            'show_join_button_on_group_pages' => Setting::where('name', 'show_join_button_on_group_pages')->first()->value,
            'are_group_codes_enabled' => Setting::where('name', 'are_group_codes_enabled')->first()->value,
            'my_groups_page_name' => Setting::where('name', 'my_groups_page_name')->first(),
            'group_admins' => Setting::where('name', 'group_admins')->first()->value,
        ]);
    }

    public function storeConfiguration(Request $request)
    {
        Setting::where('name', 'show_join_button_on_group_pages')->update([
            'value' => $request->show_join_button_on_group_pages,
        ]);
        // Setting::where('name', 'are_group_codes_enabled')->update([
        //     'value' => $request->are_group_codes_enabled,
        // ]);
        Setting::where('name', 'my_groups_page_name')->update([
            'value' => $request->input('my_groups_page_name'),
            'localization' => $request->has('localized_my_groups_page_name') ? $request->localized_my_groups_page_name : null,
        ]);
        Setting::updateOrCreate(['name' => 'group_admins'], ['value' => $request->group_admins]);

        Cache::forget('settings');
        Cache::forget('settings-for-api');

        return redirect()->back()->with('success', 'Saved!');
    }
}

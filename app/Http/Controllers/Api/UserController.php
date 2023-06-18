<?php

namespace App\Http\Controllers\Api;

use App\Post;
use App\User;
use App\Group;
use App\Setting;
use Carbon\Carbon;
use App\GroupPages;
use App\ReportedUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['createUser', 'userInfo']);
    }

    public function search(Request $request)
    {
        $groupArray = $request->user()->groups->pluck('id');
        $blockedUsers = ReportedUsers::where('reported_by', $request->user()->id)->where('status', 'blocked')->pluck('user_id');
        $whoBlockedMe = ReportedUsers::where('user_id', $request->user()->id)->where('status', 'blocked')->pluck('reported_by');
        $blockedUsersIds = $blockedUsers->merge($whoBlockedMe)->toArray();
        $results = User::where('name', 'LIKE', "%{$request->q}%")
                        ->where('is_enabled', 1)
                        ->where('is_hidden', 0)
                        ->where('id', '!=', $request->user()->id)
                        ->whereNotIn('id', $blockedUsersIds)
                        ->groupBy('users.id')
                        ->select(['name', 'id'])
                        ->distinct()
                        ->get();

        return response()->json($results);
    }

    public function isUserAdmin(Request $request)
    {
        if(!$request->has('group'))
            return User::find($request->user)->is_admin;

        return Group::find($request->group)->isUserAdmin($request->user);
    }

    public function uploadImage(Request $request)
    {
        $url = $request->file('file')[0]->store('user-images', 'public_old');

        $response = (Object) [
            "file" => (Object) [
                'url' => config('app.url') . "/uploads/" . $url,
                'id' => time(),
            ]
        ];

        return response()->json($response);
    }

    public function getResetLink($userId)
    {
        $user = User::find($userId);
        $token = $this->createToken($user);

        $url = url(config('app.url').route('password.reset', ['token' => $token, 'email' => $user->email], false));

        return response($url);
    }

    public function createToken($user)
    {
        return app('auth.password.broker')->createToken($user);
    }

    public function getUnreadNotificationCount(Request $request)
    {
        return response($request->user()->unreadNotifications()->count());
    }

    public function getNewNotifications($time, Request $request)
    {
        $time = Carbon::parse($time);

        $hasNewNotifications = $request->user()->notifications()->where('created_at', '>', $time)->exists();

        return response($hasNewNotifications);
    }

    public function userInfo(Request $request)
    {
        $user = $request->user()->loadCount('receipts');
        $groups = $user->groups;
        $locale = $user->locale;
        if($locale != 'en') {
            $groups = $groups->map(function($group) use ($locale) {
                return $group->localize($locale);
            });
        }

        $response['user'] = $user;
        $response['groups'] = $groups;
       
        $user->dashboard_groups_list = $user->localized_dashboard_groups_recursive($locale);
        $user->badges = $user->allBadges(true);
        $user->badges_groups = \App\Badge::where('name', 'Joined Three Groups')->first();
        return response()->json($response);
    }

    public function notifications(Request $request)
    {
        $user = $request->user();
        $notifications = [];

        $notifications['hasUnreadNotifications'] = $user->unreadNotifications()->count();

        return response()->json($notifications);
    }

    public function dashboardNotifications(Request $request)
    {
        $user = $request->user();
        $notifications = [];

        $notifications['unreadMessageCount'] = $user->unreadMessageCount;
        $notifications['hasIdeationNotifications'] = $user->unread_ideation_invitations->count();
        $notifications['hasIntroductionNotifications'] = $user->unreadIntroductionCount;
        $notifications['hasShoutoutNotifications'] = $user->unreadShoutoutCount;
        $notifications['hasEventNotifications'] = $user->event_notifications_count;
        $notifications['unread_message_count'] = $user->unreadMessageCount;
        $notifications['unread_introduction_count'] = $user->unread_introduction_count;
        $notifications['unread_shoutout_count'] = $user->unreadShoutoutCount;
        $notifications['unread_ideation_invitations_count'] = $user->unread_ideation_invitations->count();

        return response()->json($notifications);
    }

    public function createUser(Request $request)
    {
        if(!$request->has('user_email') || !$request->has('first_name') || (!$request->has('password') && !$request->has('user_pass')))
        {
            \App\Log::create([
                'action' => 'account not created',
                'user_id' => 1,
                'message' => $request->all()->implode(', '),
            ]);
            return response('Insufficient data.');
        }

        $password = $request->has('password') ? $request->password : $request->user_pass;

        // $password = openssl_decrypt($request->password, 'aes-128-cbc-hmac-sha256', env('USER_CREATE_KEY'), 0, 1111111111111111);

        //create user or find user with that email
        if(User::where('email', $request->email)->exists())
        {
            $user = User::where('email', $request->email)->first();
            $user->update([
                'name' => $request->first_name . ' ' . $request->last_name,
                'password' => Hash::make($password),
                'location' => $request->state_code,
                'job_title' => $request->position,
                'company' => $request->company,
                'phone' => $request->mobile,
            ]);
        }
        else
        {
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->user_email,
                'password' => Hash::make($password),
                'location' => $request->state_code,
                'job_title' => $request->position,
                'company' => $request->company,
                'phone' => $request->mobile,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);
        }

        //assign user to groups
        if($request->has('groups'))
            $user->groups()->syncWithoutDetaching($request->groups);

        return response(true);
    }

    public function peopleYouShouldKnow(Request $request)
    {
        $blockedUsersIds = get_blocked_users_ids();
        $categories = $request->user()->mostPopulatedOptions->take(6);

        $categories = $categories->map(function ($category) {
            $category->users = $category->activeUsers()
                                        ->where('users.id', '!=', request()->user()->id)
                                        ->whereNotIn('users.id', get_blocked_users_ids())
                                        ->inRandomOrder()
                                        ->take(3)->get();
            $category->users = $category->users->map(function ($user) {
                $user->photo_url = $user->photo_path;
                return $user;
            });

            return $category;
        });

        return $categories;
    }

    public function pagesShow(Request $request , $slug = null)
    {
        $pagesData = [];
        $showAll = auth()->user()->is_admin;
        $settings = Setting::where('name','=','pages')->select('value')->first();
        $is_admin_settings = Setting::where('name','=','is_pages')->select('value')->first();

        if ($slug) {
            $group = Group::where('slug', $slug)->first();
            $group_id = strVal($group->id);
            $condition = $showAll ? [] : ['visibility' => 1, 'is_active' => 1];
            $pagesData = GroupPages::where($condition)->whereJsonContains('show_in_groups', [$group_id])->get();
        } else {
            
            $condition = $showAll ? [] : ['visibility' => 1];
            $pagesData = GroupPages::where($condition)->get();
        }
       
        if ($is_admin_settings->value == 1) {
            $pages = array(
                'pages' => $pagesData,
                'settings' => $settings,
                'is_admin_settings' => $is_admin_settings,
                'is_url' => config('app.url')
            );
        } else {
            $pages = 0;
        }
        return (is_array($pages)) ? array_reverse($pages) : $pages; 
    }
    // public function showPagesGroup($slug)
    // {
       

    //     $groups = Group::where('slug', $slug)->get();
        
    //     $allgroups = array();
    //     foreach($groups as $group){
    //         $allgroups =  $group->id;
    //     }
       
    //     $groupPages =   GroupPages::where('is_active',1)->where('displayed_show','on')->whereJsonContains('show_in_groups', "$allgroups")->get(); 
    //     return response()->json($groupPages);

    // }

}

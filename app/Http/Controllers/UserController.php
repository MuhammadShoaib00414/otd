<?php

namespace App\Http\Controllers;

use DB;
use Mail;
use \Hash;
use App\Post;
use App\Setting;
use App\User;
use App\Group;
use App\Skill;
use App\Device;
use App\Keyword;
use App\Category;
use App\Question;
use App\Taxonomy;
use Carbon\Carbon;
use App\AwardedPoint;
use App\Badge;
use App\EmailResetKey;
use App\ReportedUsers;
use App\Events\SearchEvent;
use App\Events\ViewProfile;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use App\Events\UserReported;
use Illuminate\Http\Request;
use App\Events\ProfileUpdated;
use App\Events\ProfilePhotoUploaded;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['saveToken', 'deleteToken']);
        $this->middleware('onboarding')->except(['wizard', 'submitWizard', 'saveToken', 'deleteToken']);
    }

    public function profile(Request $request)
    {
        // $questions = Question::enabled()->where('locale', $request->user()->locale)->topLevel()->orderBy('order_key')->get();
        $questions = Question::enabled()->topLevel()->where('locale', $request->user()->locale)->orderBy('order_key', 'asc')->get();

        $groups = Group::where(function ($query) {
            $query->whereNull('parent_group_id')->where('is_private', 0);
        })->orWhere(function ($query) use ($request) {
            $query->whereNull('parent_group_id')->whereIn('id', $request->user()->groups()->pluck('id'));
        })->orderBy('name', 'asc')->get();

        if ($request->user()->groups()->count())
            $groups = $groups->merge($request->user()->groups()->whereNull('parent_group_id')->get());
        // if ($groups->count() == 1)
        //     $request->user()->groups()->sync($groups);
        $onboarding_settings = json_decode(getsetting('onboarding_settings'), true);
        $is_setting = [
            'is_gender' => Setting::where('name', 'is_gender')->first(),
        ];
    
        return view('users.profile')->with([
            'user' => $request->user(),
            'groups' => $groups,
            'questions' => $questions,
            'settings' => $onboarding_settings['questions']['description'],
            'setting' =>$is_setting
        ]);
    }

    public function update(Request $request)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            'name' => 'required',
            'photo' => 'file|max:51200',
        ]);
        if (!$request->user()->is_event_only) {
            $request->validate([
                'groups' => 'required|array|min:1',
            ]);
        }

        $request->user()->options()->sync($request->options);

        if ($request->has('groups'))
            $request->user()->groups()->sync($request->groups);

        if ($request->has('photo')) {
            $path = $request->file('photo')->store('profile-pictures');
            $request->user()->update([
                'photo_path' => $path,
            ]);
            event(new ProfilePhotoUploaded($request->user()));
        }

        if ($request->has('password')) {
            $request->user()->update([
                'password' => bcrypt($request->input('password')),
            ]);
        }
        if ($request->has('is_mentor') && $request->user()->badges()->where('id', '=', 5)->count() == 0) {
            $request->user()->update([
                'is_mentor' => 1,
            ]);
            if(Badge::count() > 0 && Badge::where('id', 5)->first())
                $request->user()->badges()->syncWithoutDetaching(5);
        } else if (!$request->has('is_mentor')) {
            $request->user()->update([
                'is_mentor' => 0,
            ]);
            if(Badge::count() > 0 && Badge::where('id', 5)->first())
                $request->user()->badges()->detach(5);
        }

        $request->user()->update(
            array_merge(
                $request->except(['_token', 'is_mentor', 'password', 'photo', 'categories', 'keywords', 'groups', 'skills', 'answers'])
            )
        );

        $questions = collect($request->answers)->map(function ($answer, $questionId) {
            if (is_array($answer))
                $answer = join(',', $answer);

            return ['question_id' => $questionId, 'answer' => $answer];
        });

        $request->user()->questions()->sync($questions);

        $request->user()->updateSearch();

        if (User::where('id', $request->user()->id)->whereNotNull(['name', 'pronouns', 'location', 'photo_path']))
            if(Badge::count() > 0 && Badge::where('id', 3)->first())
                $request->user()->badges()->syncWithoutDetaching(3);

        event(new ProfileUpdated($request->user()));

        return redirect('/users/' . $request->user()->id);
    }

    public function show($id, Request $request)
    {
        if ($request->has('unblock') && $request->unblock) {
            ReportedUsers::where('reported_by', $request->user()->id)->where('user_id', $id)->where('status', 'blocked')->delete();
        }

        $user = User::find($id);
        if (!$user)
            abort(404);
        $isBlocked = ReportedUsers::where('user_id', $user->id)
            ->where('reported_by', $request->user()->id)
            ->where('status', 'blocked')
            ->count();
        $blocked = ReportedUsers::where('reported_by', $user->id)
            ->where('user_id', $request->user()->id)
            ->where('status', 'blocked')
            ->count();
        if ($isBlocked > 0 || $blocked > 0)
            abort(404);
        event(new ViewProfile($request->user(), $user));

        if (!$user->badges()->where('name', 'Mentor')->count() && $user->is_mentor)
            $user->badges()->syncWithoutDetaching(\App\Badge::where('name', 'Mentor')->first());

        return view('users.show')->with([
            'user' => $user,
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        $group = $request->input('group');

        if ($group) {
            $groupArray = [$group];
            $groupDisplay = Group::find($group)->name;
        } else {
            $groupArray = $request->user()->groups->pluck('id');
            $groupDisplay = "all your groups";
        }

        $results = User::leftJoin('group_user', 'group_user.user_id', '=', 'users.id')
            ->where([
                ['is_enabled',      '=', '1'],
                ['is_hidden',       '=', '0'],
                ['job_title',       '!=', ''],
            ])->whereIn('group_user.group_id', $groupArray)
            ->whereNotNull('job_title')
            ->where(function ($eQuery) use ($query) {
                $eQuery->where('name', 'LIKE', '%' . $query . '%')
                    ->orWhere('job_title', 'LIKE', '%' . $query . '%')
                    ->orWhere('company', 'LIKE', '%' . $query . '%')
                    ->orWhere('location', 'LIKE', '%' . $query . '%')
                    ->orWhere('search', 'LIKE', '%' . $query . '%');
            })
            ->groupBy('users.id')
            ->select('id', 'name', 'photo_path', 'job_title', 'company', 'location');

        event(new SearchEvent($request->user(), $query));

        return view('users.search')->with([
            'results' => $results->get(),
            'q' => $query,
            'groupDisplay' => $groupDisplay,
        ]);
    }

    public function wizard(Request $request)
    {

        Cache::flush();
        $frequencyOptions = [
            'immediately',
            'daily',
        ];
        $groups = Group::where(function ($query) {
            $query->whereNull('parent_group_id')->where('is_private', 0);
          
        })->orWhere(function ($query) use ($request) {
            $query->whereNull('parent_group_id')->whereIn('id', $request->user()->groups()->pluck('id'));
        })->orderBy('name', 'asc')->get();
  
        if ($request->user()->groups()->count())
            $groups = $groups->merge($request->user()->groups()->whereNull('parent_group_id')->get());
        if ($groups->count() == 1) {
            $request->user()->groups()->syncWithoutDetaching($groups);
        }
     
        if ($request->user()->is_event_only == 1)
        // $groups = Group::whereIn('id',$request->user()->groups()->pluck('id')->toArray())->get();
            $groups = collect([]);
       
        $questions = Question::enabled()->topLevel()->where('locale', $request->user()->locale)->orderBy('order_key', 'asc')->get();

        $onboarding_settings = json_decode(getsetting('onboarding_settings'), true);
        $frequencyOptions = [
            'immediately',
            'daily',
        ];
        $agent = new Agent();
        $deviceType = $agent->device() . ', ' . $agent->browser();
        $deviceName = $agent->platform();
        $isRegistered = $request->user()->devices()
            ->where('device_name', $agent->device() . ', ' . $agent->browser())
            ->where('device_type', $agent->platform())->count();
        
        $is_setting = [
            'is_name_lable' => Setting::where('name', 'is_name_lable')->first(),
            'is_gender_required' => Setting::where('name', 'is_gender_required')->first(),
            'is_gender' => Setting::where('name', 'is_gender')->first(),
            'is_location_required' => Setting::where('name', 'is_location_required')->first(),
            'is_location' => Setting::where('name', 'is_location')->first(),
        ];
      
      
        return view('onboarding.wizard')->with([
            'groups' => $groups,
            'questions' => $questions,
            'frequencyOptions' => $frequencyOptions,
            'settings' => $onboarding_settings,
            'isRegistered' => $isRegistered,
            'user' => $request->user(),
            'frequencyOptions' => $frequencyOptions,
            'setting' =>$is_setting
        ]);
    }

    public function submitWizard(Request $request)
    {
     
        Cache::flush();
        if ($request->has('options'))
            $request->user()->options()->sync($request->options);

        if ($request->has('keywords'))
            $request->user()->keywords()->syncWithoutDetaching($request->input('keywords'));

        if ($request->has('groups'))
            $request->user()->groups()->syncWithoutDetaching($request->input('groups'), false);

        if ($request->has('is_visible'))
              $request->user()->update(['is_hidden' => ($request->is_visible == 1) ? 0 : 1]);

        if ($request->has('skills'))
            $request->user()->skills()->syncWithoutDetaching($request->input('skills'));

        if ($request->has('photo')) {
            $request->user()->update([
                'photo_path' => $request->file('photo')->store('profile-pictures'),
            ]);
            event(new ProfilePhotoUploaded($request->user()));
        }

        if ($request->has('answers')) {
            $questions = collect($request->answers)->map(function ($answer, $questionId) {
                if (is_array($answer))
                    $answer = join(',', $answer);

                return ['question_id' => $questionId, 'answer' => $answer];
            });

            $request->user()->questions()->sync($questions);
        }

        if ($request->has('phone')) {
            $request->user()->update(['phone' => '+1' . $request->phone]);
        }

        if ($request->has('is_mentor')) {
            $request->user()->update([
                'is_mentor' => 1,
            ]);
        } else {
            $request->user()->update([
                'is_mentor' => 0,
            ]);
        }

        $request->user()->update(
            array_merge(
                $request->except(['_token', 'is_mentor', 'password', 'photo', 'categories', 'keywords', 'groups', 'skills', 'phone', 'enable_push_notifications', 'is_visible'])
            )
        );

        $request->user()->update([
            'is_onboarded' => 1,
            'enable_push_notifications' => $request->has('enable_push_notifications'),
            'is_hidden' =>  getSetting('hide_new_members'),
        ]);

        $request->user()->updateSearch();

        return redirect('/users/' . $request->user()->id);
    }

    public function browse(Request $request)
    {
        $request->user()->logs()->create([
            'action' => 'used Find My People',
        ]);

        $backlink = null;
        $group = null;
        if ($request->group) {
            $group = Group::find($request->group);
            $backlink = [
                'url' => '/groups/' . $group->slug . '/lounge',
                'text' => $group->name . ' - Networking Lounge',
            ];
        }
        if ($request->all() != []) {
            $groupArray = $request->user()->groups->pluck('id');
            $userIds = [];
            if ($request->has('options')) {
                $options = $request->options;
                $userIds = collect(DB::select(DB::raw("SELECT option_user.user_id FROM option_user where option_id in (" . join(',', $options) . ") group by user_id having count(distinct option_id) = " . count($options))))->pluck('user_id');
                if ($group)
                    $results = $group->users()->whereIn('id', $userIds)->where('users.id', '!=', $request->user()->id)->where('is_hidden', 0)->where('is_enabled', 1)->with(['options'])->get();
                else
                    $results = User::whereIn('id', $userIds)->where('users.id', '!=', $request->user()->id)->where('is_hidden', 0)->where('is_enabled', 1)->with(['options'])->get();
            } else {
                if ($group)
                    $results = $group->users()->where('users.id', '!=', $request->user()->id)->where('is_hidden', 0)->where('is_enabled', 1)->with(['options'])->get();
                else
                    $results = User::where('users.id', '!=', $request->user()->id)->where('is_hidden', 0)->where('is_enabled', 1)->with(['options'])->get();
            }
        } else {
            $results = [];
        }

        $taxonomies = Taxonomy::where('is_enabled', 1)->orderBy('browse_order_key')->get();
        $taxonomies = $taxonomies->map(function ($taxonomy) use ($group) {
            $taxonomy->list = $taxonomy->orderedGroupedOptionsWithUsersBelongingToGroup($group);
            return $taxonomy;
        })->filter(function ($taxonomy) {
            return $taxonomy->list->count();
        });

        return view('browse.results')->with([
            'results' => $results,
            'taxonomies' => $taxonomies,
            'backlink' => $backlink,
            'group' => $group,
        ]);
    }

    public function points(Request $request)
    {
        $user = $request->user();

        if (!getsetting('is_points_enabled') && !$user->is_admin)
            return redirect('/home');

        $awardedPoints = AwardedPoint::where('user_id', '=', $user->id)->orderBy('id', 'desc')->paginate(15);

        return view('points.index')->with([
            'awardedPoints' => $awardedPoints,
        ]);
    }

    public function account(Request $request)
    {
        $frequencyOptions = [
            'immediately',
            'daily',
        ];
        $agent = new Agent();
        $deviceType = $agent->device() . ', ' . $agent->browser();
        $deviceName = $agent->platform();
        $isRegistered = $request->user()->devices()
            ->where('device_name', $agent->device() . ', ' . $agent->browser())
            ->where('device_type', $agent->platform())->count();
        return view('users.account')->with([
            'user' => $request->user(),
            'frequencyOptions' => $frequencyOptions,
            'isRegistered' => $isRegistered,
        ]);
    }

    public function updateAccount(Request $request)
    {
        if ($request->has('new_password')) {
            if ($request->new_password != $request->new_password_confirm)
                return redirect()->back()->withErrors(['msg' => 'New password must match confirmation entry.']);

            if (Str::length($request->new_password) < 8)
                return redirect()->back()->withErrors(['msg' => 'New password must contain at least 8 characters.']);

            $request->user()->update([
                'password' => Hash::make($request->new_password),
            ]);

            return redirect('/account')->with([
                'success' => 'Account password changed!'
            ]);
        }
        if ($request->notification_method == 'sms' && empty($request->phone)) {
            return redirect()->back()->withErrors([
                'msg' => 'Error: You must enter a cell phone number to enable text message notifications.'
            ]);
        }
        if ($request->notification_method == 'sms') {
            $validator = Validator::make($request->all(), []);
            $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
            $phone = $phoneUtil->parse($request->phone, "US");
            if (!$phoneUtil->isValidNumber($phone)) {
                $validator->errors()->add('phone', 'Invalid phone number!');
                return back()->withErrors($validator);
            }
            $out = $phoneUtil->format($phone, \libphonenumber\PhoneNumberFormat::E164);

            $request->user()->update([
                'notification_frequency' => $request->has('notification_frequency') ? $request->notification_frequency : 'immediately',
                'notification_method' => $request->notification_method,
                'phone' => $out,
                'locale' => $request->has('locale') ? $request->locale : 'en',
            ]);
        } else {
            $request->user()->update([
                'notification_frequency' => $request->has('notification_frequency') ? $request->notification_frequency : 'immediately',
                'notification_method' => $request->notification_method,
                'locale' => $request->has('locale') ? $request->locale : 'en',
                'enable_push_notifications' => $request->has('enable_push_notifications'),
            ]);
        }

        return redirect('/account')->with([
            'success' => 'Account settings succesfully updated!'
        ]);
    }

    public function updateGDPR(Request $request)
    {
        $request->user()->update(['is_hidden' => ($request->is_visible == 1) ? 0 : 1]);

        return redirect('/account')->with([
            'success' => 'Account settings succesfully updated!'
        ]);
    }

    public function updateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        if (User::where('email', $request->email)->exists())
            return redirect()->back()->withErrors('This email address is already taken.');

        $emailResetKey = EmailResetKey::create([
            'email' => $request->email,
            'key' => Str::random(10),
        ]);

        Mail::to($request->email)->send(new \App\Mail\EmailVerification($request->user(), $emailResetKey->key));

        return redirect('/account')->with([
            'success' => 'A verification email has been sent to ' . $request->email,
        ]);
    }

    public function verifyEmail($key, Request $request)
    {
        if (!EmailResetKey::keyExists($key))
            return redirect('/account')->withErrors('Sorry, this token is invalid.');

        $emailReset = EmailResetKey::findKey($key);

        $request->user()->update([
            'email' => $emailReset->email,
        ]);

        $emailReset->verify();

        return redirect('/account')->with([
            'success' => 'Your email address has been changed.',
        ]);
    }

    public function redirectToShowUser(Request $request)
    {
        return redirect()->to('/users/' . $request->user()->id);
    }

    public function updateTimezone(Request $request)
    {
        $request->user()->update(['timezone' => $request->timezone]);
    }

    public function saveToken(Request $request)
    {
        $user = isset($request->userId) ? User::find($request->userId) : $request->user();

        if (!$user)
            return response()->json([
                'message' => 'token not saved. User not found.',
                'status' => 500,
            ]);
        $device = $user->devices()->where('token', $request->token)->first();
        if (!$device) {
            if ($request->has('type') && $request->type == 'this_device') {
                $agent = new Agent();
                $device = Device::create([
                    'user_id' => $user->id,
                    'token' => $request->token,
                    'device_type' => $agent->platform(),
                    'device_name' => $device = $agent->device() . ', ' . $agent->browser(),
                    'active' => true,
                ]);
            } else {
                $device = Device::create([
                    'user_id' => $user->id,
                    'token' => $request->token,
                    'device_type' => $request->device_type,
                    'device_name' => $request->device_name,
                    'active' => true
                ]);
            }
        } else {
            return response()->json([
                'message' => 'token not saved. Device already exists.',
                'data' => $device,
                'status' => 500,
            ]);
        }

        return response()->json([
            'message' => 'token saved successfully.',
            'data' => $device,
            'status' => 200
        ]);
    }

    public function deleteToken(Request $request)
    {
        $user = isset($request->userId) ? User::find($request->userId) : $request->user();
        if ($request->has('userId') && $request->has('token')) {
            $devices = $user->devices;
            $currentDevice = $devices->where('token', $request->token)->first();
            if ($currentDevice) {
                $currentDevice->delete();
                return response()->json(['message' => 'token deleted successfully.', 'status' => 200]);
            }
            return response()->json(['message' => 'token not deleted. Device not found.', 'status' => 500]);
        }
        return response()->json(['message' => 'Missing parameters.', 'status' => 500]);
    }

    public function pushNotification(Request $request, $id)
    {
        $user = $request->user();
        $device = $user->devices()->where('id', $id)->first();
        if ($request->has('type') && $request->type == 'remove_device') {
            $device->delete();
            return response()->json(['message' => 'Device removed successfully.', 'status' => 200]);
        } elseif ($request->has('type') && $request->type == 'add_device') {
            $device->update([
                'active' => true,
                'inactive_reason' => "manual"
            ]);
            return response()->json(['message' => 'Push notification activated successfully.', 'status' => 200]);
        } elseif ($request->has('type') && $request->type == 'disable_device') {
            $device->update([
                'active' => false,
                'inactive_reason' => "manual"
            ]);
            return response()->json(['message' => 'Push notification deactivated successfully.', 'status' => 200]);
        }
        return response()->json(['message' => 'No action specified.', 'status' => 500]);
    }

    public function verifyNotificationToken($token)
    {
        $device = Device::where('token', $token)->first();
        if ($device) {
            return response()->json(['message' => 'verified', 'status' => 200]);
        }
        return response()->json(['message' => 'not verified', 'status' => 500]);
    }


    /**
     * Report/Block the user
     * @param int $postId
     * @return \Illuminate\Http\JsonResponse
     */

    public function reportUser(Request $request, $postId)
    {
        $post = Post::find($postId);
        $userId = $request->user()->id;
        $userId = $post->post_type::find($post->post_id)->user_id;
        $userToReport = User::find($userId);
        return view('groups.posts.report-user', compact('userId', 'userToReport', 'postId'));
    }

    /**
     * Report/Block the user
     * @param int $userId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function postReportUser(Request $request, $userId)
    {
        $reportedUser = User::find($request->user_id);
        if (!$reportedUser) {
            return redirect('/home');
        }
        $record = ReportedUsers::where('user_id', $request->user_id)->where('reported_by', $request->user()->id)->first();
        if ($record) {
            return redirect('/home');
        }
        $reportedBy = $request->user()->id;
        ReportedUsers::create([
            'user_id' => $request->user_id,
            'reported_by' => $reportedBy,
            'reason' => $request->get('report-reason'),
            'status' => ($request->has('block')) ? 'blocked' : 'reported',
        ]);
        event(new UserReported($reportedUser, $request->user()));
        return redirect('/home');
    }

    /**
     * Blocked users list
     */

    function blockedUsers(Request $request)
    {
        $user = User::find($request->user()->id);
        $blockedUsers = $user->blockedUsers();
        return view('users.blocked-users', compact('user', 'blockedUsers'));
    }

    /**
     * Block user
     * @param int $userId
     */

    function blockUser($id, Request $request)
    {
        $userId = $request->user()->id;
        $userToReport = User::find($id);
        $postId = null;
        return view('groups.posts.report-user', compact('userId', 'userToReport', 'postId'));
    }
    public function deleteAccount($id, Request $request)
    {

        $user = User::find($id);
        $delete_at =   $user->update([
            'deleted_at' => Carbon::now(),
        ]);
        return redirect('/account')->with([
            'success' => 'After 30 days, your account and all your information will be permanently deleted, and you wont be able to retrieve your information.',
        ]);
    }
}

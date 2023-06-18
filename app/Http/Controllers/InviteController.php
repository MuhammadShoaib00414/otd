<?php

namespace App\Http\Controllers;

use App\Log;
use App\User;
use Location;
use App\Group;

use Carbon\Carbon;
use App\Invitation;
use App\Events\ViewProfile;
use App\RegisterInvitation;
use Illuminate\Http\Request;
use App\Events\InvitationUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class InviteController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware('locale')->only('show');
        Cache::flush();
    }

    public function show($slug, Request $request)
    {
  
        $invite_slug = RegisterInvitation::where('unique_id', '=', $slug)->first();
       
        if(!empty($invite_slug)){
           
            return view('invitation.expiredPage');
        }else{
            $invite = Invitation::where('hash', '=', $slug)->first();
        if (empty($invite) || $invite->revoked_at != null || $invite->accepted_at != null)
                return redirect('/login');
            return view('invitation.accept')->with([
                'invite' => $invite,
        ]);
    }
    }

    public function accept($slug, Request $request)
    {
   
   
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'g-recaptcha-response' => 'required|captcha',
        ]);

        $invite = Invitation::where('hash', '=', $slug)->first();
        $inviteuser = Log::where('message', '=', $slug)->first();
        // $add_group = json_decode($invite->add_to_groups);
        $group_of_admin_data = array();
        if(!empty($invite->add_to_groups) && !empty($invite->groups_admin_of)){
            $add_groupofadmin = json_decode($invite->groups_admin_of,true);
            $group_of_admin_data = array_diff($add_groupofadmin,$invite->add_to_groups);
        }else{

            if(is_array($invite->add_to_groups) && @count($invite->add_to_groups) > 0)
            {
                $group_of_admin_data = json_decode($invite->groups_admin_of,true);
            }
            else if(is_array($invite->groups_admin_of) && @count($invite->groups_admin_of) > 0)
            {
                $group_of_admin_data = json_decode($invite->groups_admin_of,true);
            }
           
        }
        
        if (empty($invite) || $invite->revoked_at != null || $invite->accepted_at != null)
            return "Sorry, that link doesn't work.";

        if ( User::where('email', '=', $request->input('email'))->count() )
            return 'That email is already associated with an account.';

        $user = User::create([
            'email' => $request->input('email'),
            'name' => $request->input('name'),
            'password' => bcrypt($request->input('password')),
            'is_event_only' => $invite->is_event_only,
            'locale' => $request->has('locale') ? $request->locale : 'en',
            'is_hidden'=> getsetting('hide_new_members')
        ]);
        if(isset($group_of_admin_data)) {
            $insert = [];
            foreach($group_of_admin_data as $data) 
            {
                $draw = [
                    'user_id' =>$user->id,
                    'group_id' =>$data,
                    'is_admin' => 1,
                    'expires_at' => Carbon::now(),
                ];
                $insert[] = $draw;
            }
            \DB::table('group_user')->insert($insert);
        }
        $logsRegisteration = Log::create([
            'action'             => 'Registeration',
            'user_id' =>$inviteuser->user_id,
            'message' => 'Register a new user by invitation Email :' . $request->input('email'),
            'related_model_type' => 'App\User',
            'related_model_id' =>$inviteuser->user_id,
            'track_info' =>  UserTrackInfo(),
        ]);
        $logs = Log::create([
            'action'             => 'Invitation link',
            'user_id' => $user->id,
            'message' => 'Register through a invitation link',
            'related_model_type' => 'App\User',
            'related_model_id' =>$user->id,
            'track_info' =>  UserTrackInfo(),
        ]);

        $RegisterInvitation = RegisterInvitation::create([
            'register_page_id' => 0,
            'unique_id' => $slug,
            'expired_id' => 1,
            'user_id' => $user->id,
            'register_date_time' => Carbon::now(),

        ]);

    
        if($invite->add_to_groups)
        {
            $groups = [];
            foreach($invite->add_to_groups as $groupId)
            {
                if($invite->expires_at)
                    $groups[$groupId] = ['expires_at' => $invite->expires_at, 'is_admin' => $invite->groups_admin_of ? collect(json_decode($invite->groups_admin_of))->contains($groupId) : 0];
                else
                    $groups[$groupId] = ['is_admin' => $invite->groups_admin_of ? collect(json_decode($invite->groups_admin_of))->contains($groupId) : 0];
            }
            $user->groups()->attach($groups);
        }

        $invite->update([
            'accepted_at' => \Carbon\Carbon::now(),
        ]);

        event(new \Illuminate\Auth\Events\Registered($user));

        \Auth::login($user);

        return view('invitation.success');
    }
}

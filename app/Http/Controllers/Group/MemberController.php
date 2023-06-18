<?php

namespace App\Http\Controllers\Group;

use Session;
use App\Post;
use App\User;
use App\Group;
use App\Shoutout;
use App\Events\ShoutoutMade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class MemberController extends Controller
{

    public function __construct()
    {
        
        $this->middleware('groupadmin')->only(['manage', 'toggleAdmin', 'remove']);
        $this->middleware('group');
        Cache::flush();
    }

    public function index(Request $request, $slug)
    {
        $group = Group::where('slug', '=', $slug)->first();

        $members = $group->activeUsers();

        if ($request->has('q')) {
            // DB::enableQueryLog();


            $members = $members->where(function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->q . '%')
                    ->orWhere('job_title', 'like', '%' . $request->q . '%')
                    ->orWhere('location', 'like', '%' . $request->q . '%')
                    ->orWhere('company', 'like', '%' . $request->q . '%');
            });

       
            // $query = DB::getQueryLog();
            // dd($query);
        }
        Cache::flush();
        $request->user()->logs()->create([
            'action' => 'viewed members',
            'related_model_type' => 'App\Group',
            'related_model_id' => $group->id,
        ]);
       
        return view('groups.members.index')->with([
            'group' => $group,
            'members' => $members->distinct()->paginate(18),
        ]);
    }

    public function manage(Request $request, $slug)
    {
        $group = Group::where('slug', '=', $slug)->first();
    
        return view('groups.members.manage')->with([
            'group' => $group,
            'users' => $group->users()->where('is_enabled', '=', '1')->where('id', '!=', Auth::user()->id)->orderBy('name', 'asc')->paginate(50),
        ]);
    }

    public function toggleAdmin(Request $request, $slug)
    {
        if ($request->has('targetuser')) {
            $group = Group::where('slug', '=', $slug)->first();
            $user = User::find($request->targetuser);

            $isGroupAdmin = $group->isGroupAdmin($user->id);
            $group->users()->updateExistingPivot($user->id, [
                'is_admin' => $isGroupAdmin ? 0 : 1,
            ]);

            Session::flash('message', "{$user->name} changed to " . ($isGroupAdmin ? 'regular member.' : 'group admin.'));
        }

        return redirect("groups/{$group->slug}/members/manage");
    }

    public function remove(Request $request, $slug)
    {
        if ($request->has('targetuser')) {
            $group = Group::where('slug', '=', $slug)->first();
            $user = User::find($request->targetuser);

            $user->groups()->detach($group);

            Session::flash('message', "{$user->name} removed from group.");
        }

        return redirect("groups/{$group->slug}/members/manage");
    }

    public function add($slug, Request $request)
    {
        $group = Group::where('slug', $slug)->first();

        return view('groups.members.add')->with([
            'users' => User::orderBy('name')->whereNotIn('id', $group->users()->pluck('id'))->where('id', '!=', $request->user()->id)->get(),
            'group' => $group,
        ]);
    }

    public function addUser($slug, Request $request)
    {
        $group = Group::where('slug', $slug)->first();
        $group->users()->attach($request->user);

        return redirect('/groups/' . $slug . '/members');
    }

    public function join($slug, Request $request)
    {
        $group = Group::where('slug', $slug)->first();
        $group->users()->attach($request->user()->id);

        return redirect('/groups/' . $slug);
    }
}

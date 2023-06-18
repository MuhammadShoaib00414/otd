<?php

namespace App\Http\Controllers\Group;

use App\Post;
use App\Group;
use App\Shoutout;
use Carbon\Carbon;
use App\Events\ShoutoutMade;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
class ShoutoutController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'group']);
        Cache::flush();
    }
    
    public function index(Request $request, $slug)
    {
        $group = Group::where('slug', '=', $slug)->first();

        $request->user()->logs()->create([
            'action' => 'viewed shoutouts',
            'related_model_type' => 'App\Group',
            'related_model_id' => $group->id,
        ]);

        return view('groups.shoutouts.index')->with([
            'group' => $group
        ]);
    }

    public function create(Request $request, $slug)
    {
      
        $group = Group::where('slug', '=', $slug)->first();

        if(!$group->isUserAdmin($request->user()->id) && !$request->user()->is_admin && !$group->can_users_post_shoutouts)
            return redirect('/groups/'.$slug);

        $recipients = $group->users()->where('id', '<>', $request->user()->id)->where('is_hidden', '=', 0)->get()->unique()->map(function ($user) {
           
            return (object) [
                'label' => $user->name,
                'value' => $user->id,
            ];
            
        });
        Cache::flush();
        return view('groups.shoutouts.create')->with([
            'group' => $group,
            'recipients' => $recipients,
        ]);
    }

    public function store(Request $request, $slug)
    {
        $validate = $request->validate([
            'reason' => 'required',
            'recipient' => 'required',
        ]);

        $created_at_timezoned = Carbon::now()->tz($request->user()->timezone);

        $group = Group::where('slug', '=', $slug)->first();

        if(!$group->isUserAdmin($request->user()->id) && !$request->user()->is_admin && !$group->can_users_post_shoutouts)
            return redirect('/groups/'.$slug);

        $shoutout = Shoutout::create([
            'shoutout_by' => $request->user()->id,
            'shoutout_to' => $request->input('recipient'),
            'body' => $request->input('reason'),
            'created_at' => $created_at_timezoned->tz('UTC')->toDateTimeString(),
        ]);
        
        $post = Post::create([
            'post_type' => get_class($shoutout),
            'post_id' => $shoutout->id,
            'group_id' => $group->id,
        ]);
      
        $post->groups()->attach($group->id);

        if($request->has('post_as_group'))
        {
            $post->update([
                'posted_as_group_id' => $group->id,
            ]);
        }

        $group->resetOrderedPosts();

        event(new ShoutoutMade($request->user(), $post));

        return redirect("/groups/{$slug}");
    }
    public function edit(Request $request, $slug,$id)
    {
        $group = Group::where('slug', '=', $slug)->first();
        $shoutout = Shoutout::where('id', '=', $id)->first();
        if(!$group->isUserAdmin($request->user()->id) && !$request->user()->is_admin && !$group->can_users_post_shoutouts)
            return redirect('/groups/'.$slug);

        $recipients = $group->users()->where('id', '<>', $request->user()->id)->get()->unique()->map(function ($user) {
            return (object) [
                'label' => $user->name,
                'value' => $user->id,
            ];
        });
        $shoutoutUsername = $group->users()->select('name')->where('id', '=', $shoutout->shoutout_to)->first();
        return view('groups.shoutouts.edit')->with([
            'group' => $group,
            'recipients' => $recipients,
            'shoutout' => $shoutout,
            'shoutoutUsername' => $shoutoutUsername->name
        ]);
    }

     public function UpdateShoutout($slug,$id, Request $request)
    {
        $validate = $request->validate([
            'reason' => 'required',
            // 'recipient' => 'required',
        ]);
     
        $created_at_timezoned = Carbon::now()->tz($request->user()->timezone);
        $group = Group::where('slug', '=', $slug)->first();
        if(!$group->isUserAdmin($request->user()->id) && !$request->user()->is_admin && !$group->can_users_post_shoutouts)
            return redirect('/groups/'.$slug);
        $shoutout = Shoutout::find($id);
        if($request->recipient == null){
            $recipient =  $shoutout->shoutout_to;
        }else{
            $recipient  = $request->input('recipient');
        }
        $shoutout->update([
            'shoutout_by' => $request->user()->id,
            'shoutout_to' =>  $recipient,
            'body' => $request->input('reason'),
            'created_at' => $created_at_timezoned->tz('UTC')->toDateTimeString(),
        ]); 
        $post = Post::where('post_id', '=', $id)->where('post_type','=', get_class($shoutout))->first();
        $post->update([
            'post_at' => carbon::now()->toDateTimeString(),
        ]); 
        $post->groups()->attach($group->id);

        if($request->has('post_as_group'))
        {
            $post->update([
                'posted_as_group_id' => $group->id,
            ]);
        }

        $group->resetOrderedPosts();

        event(new ShoutoutMade($request->user(), $post));

        return redirect("/home");
    }

}

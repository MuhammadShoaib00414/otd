<?php

namespace App\Http\Controllers;

use App\Post;
use App\User;
use App\Group;
use App\Notification;
use App\Events\ShoutoutMade;
use App\Shoutout;
use Illuminate\Http\Request;

class ShoutoutsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function sent(Request $request)
    {
        $shoutouts = Shoutout::where('shoutout_by', '=', $request->user()->id)->orderBy('id', 'desc')->get();

        return view('shoutouts.sent')->with([
            'shoutouts' => $shoutouts,
        ]);
    }

    public function received(Request $request)
    {
        $shoutouts = Shoutout::where('shoutout_to', '=', $request->user()->id)->orderBy('id', 'desc')->get();
        Notification::where('user_id', '=', $request->user()->id)
                    ->where('notifiable_type', '=', 'App\Shoutout')
                    ->update(['viewed_at' => \DB::raw('NOW()')]);

        return view('shoutouts.received')->with([
            'shoutouts' => $shoutouts,
        ]);
    }

    public function create(Request $request)
    {
        $groups = $request->user()->groups()->where('is_shoutouts_enabled', 1)->where('can_users_post_shoutouts', 1)->pluck('name', 'id');
        $recipients = User::where('is_hidden', '0')->where('is_enabled', 1)->where('id', '<>', $request->user()->id)->get()->unique()->map(function ($user) use ($groups) {
            return (object) [
                'label' => $user->name,
                'value' => $user->id,
                'groups' => $user->groups()->whereIn('id', $groups->keys())->pluck('id', 'name'),
            ];
        });

        return view('shoutouts.create')->with([
            'groups' => $groups,
            'recipients' => $recipients,
        ]);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'reason' => 'required',
            'recipient' => 'required',
        ]);

        $shoutout = Shoutout::create([
            'shoutout_by' => $request->user()->id,
            'shoutout_to' => $request->input('recipient'),
            'body' => $request->input('reason'),
        ]);
        
        $post = Post::create([
            'post_type' => get_class($shoutout),
            'post_id' => $shoutout->id,
        ]);

        if($request->has('groups'))
            $post->groups()->sync($request->groups);

        if($request->has('users'))
            $post->users()->sync($request->users);

        event(new ShoutoutMade($request->user(), $post));

        return redirect("/shoutouts/sent");
    }
}

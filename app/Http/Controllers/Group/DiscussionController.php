<?php

namespace App\Http\Controllers\Group;

use App\Log;
use App\Post;
use App\Group;
use App\ReportedPost;
use App\DiscussionPost;
use App\DiscussionThread;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\Discussions\NewDiscussion;
use App\Events\Discussions\DiscussionEdit;
use App\Events\Discussions\DiscussionDeleted;
use App\Events\Discussions\DiscussionReplied;
use App\Events\Discussions\DiscussionPostReported;

class DiscussionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'group']);
    }
    
    public function index($groupSlug, Request $request)
    {
        $group = Group::where('slug', '=', $groupSlug)->first();
        if ($request->has('q')) {
            $discussions = $group->discussions()->whereHas('posts', function ($query) use ($request) {
                $query->where('body', 'like', '%'.$request->input('q').'%');
            })->orderBy('updated_at', 'desc')->simplePaginate(20);
        } else {
            $discussions = $group->discussions()->orderBy('updated_at', 'desc')->simplePaginate(20);
        }

        $request->user()->logs()->create([
            'action' => 'viewed discussions',
            'related_model_type' => 'App\Group',
            'related_model_id' => $group->id,
        ]);

        return view('groups.discussions.index')->with([
            'group' => $group,
            'discussions' => $discussions,
        ]);
    }

    public function create($groupSlug)
    {
        $group = Group::where('slug', '=', $groupSlug)->first();

        return view('groups.discussions.create')->with([
            'group' => $group,
        ]);
    }

    public function store($groupSlug, Request $request)
    {
        $validate = $request->validate([
            'name' => 'required',
            'body' => 'required',
        ]);

        $group = Group::where('slug', '=', $groupSlug)->first();

        $discussion = DiscussionThread::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
            'group_id' => $group->id,
            'user_id' => $request->user()->id,
        ]);
        $discussion->update([
            'slug' => Str::slug($request->name, '-') . "-" . $discussion->id
        ]);
        DiscussionPost::create([
            'body' => $request->input('body'),
            'discussion_thread_id' => $discussion->id,
            'user_id' => $request->user()->id
        ]);

        $post = Post::create([
            'post_type' => get_class($discussion),
            'post_id' => $discussion->id,
            'group_id' => $group->id,
        ]);
        $post->groups()->attach($group->id);

        event(new NewDiscussion($request->user(), $discussion, $group));
        $group->resetOrderedPosts();

        return redirect('/groups/'.$groupSlug.'/discussions/'.$discussion->slug);
    }

    public function show($groupSlug, $discussionSlug, Request $request)
    {
        $group = Group::where('slug', '=', $groupSlug)->first();
        if (!$group)
            return errorView('This group no longer exists.');
        $discussion = DiscussionThread::where('slug', '=', $discussionSlug)->first();
        if (!$discussion)
            return errorView('This discussion thread no longer exists.');

        event(new \App\Events\Discussions\DiscussionViewed($request->user(), $discussion));

        return view('groups.discussions.show')->with([
            'group' => $group,
            'discussion' => $discussion,
        ]);
    }

    public function edit($groupSlug, $discussionSlug)
    {
        $group = Group::where('slug', '=', $groupSlug)->first();
        $discussion = DiscussionThread::where('slug', '=', $discussionSlug)->first();

        return view('groups.discussions.edit')->with([
            'group' => $group,
            'discussion' => $discussion,
        ]);
    }

    public function update($groupSlug, $discussionSlug, Request $request)
    {
        $request->validate([
            'name' => 'required|max:250'
        ]);
        $discussion = DiscussionThread::where('slug', '=', $discussionSlug)->first();

        $discussion->update([
            'name' => $request->input('name'),
        ]);

        $group = Group::where('slug', '=', $groupSlug)->first();

        event(new DiscussionEdit($request->user(), $discussion, $group));

        return redirect('/groups/'.$groupSlug.'/discussions/'.$discussionSlug);
    }

    public function postReply($groupSlug, $discussionSlug, Request $request)
    {
        $validate = $request->validate([
            'body' => 'required',
        ]);

        $group = Group::where('slug', '=', $groupSlug)->first();
        $discussion = DiscussionThread::where('slug', '=', $discussionSlug)->first();
	    $discussion->update(['updated_at' => \Carbon\Carbon::now()]);
        $post = DiscussionPost::create([
            'body' => $request->input('body'),
            'discussion_thread_id' => $discussion->id,
            'user_id' => $request->user()->id
        ]);
        $discussion->listing()->update([
            'post_at' => \Carbon\Carbon::now(),
            'created_at' => \Carbon\Carbon::now(),
        ]);

        $group->resetOrderedPosts();

        event(new DiscussionReplied($request->user(), $discussion, $group));
	
        return redirect('/groups/'.$groupSlug.'/discussions/'.$discussionSlug);
    }

    public function deleteThread($groupSlug, $discussionSlug, Request $request)
    {
        $group = Group::where('slug', '=', $groupSlug)->first();

        $discussion = DiscussionThread::where('slug', '=', $discussionSlug)->first();
        $discussion->notifications()->delete();
        $discussion->delete();

        event(new DiscussionDeleted($request->user(), $discussion, $group));

        return redirect('/groups/'.$groupSlug.'/discussions');
    }

    public function editPost($groupSlug, $discussionSlug, $postId)
    {
        $group = Group::where('slug', '=', $groupSlug)->first();
        $discussion = DiscussionThread::where('slug', '=', $discussionSlug)->first();
        $post = DiscussionPost::find($postId);

        return view('groups.discussions.posts.edit')->with([
            'group' => $group,
            'discussion' => $discussion,
            'post' => $post,
        ]);
    }

    public function updatePost($groupSlug, $discussionSlug, $postId, Request $request)
    {
        $post = DiscussionPost::where('id', '=', $postId)->update([
            'body' => $request->input('body'),
        ]);

        return redirect('/groups/'.$groupSlug.'/discussions/'.$discussionSlug);
    }

    public function deletePost($groupSlug, $discussionSlug, $postId, Request $request)
    {
        $post = DiscussionPost::find($postId);
        $post->reported()->delete();
        $post->delete();

        DiscussionThread::where('slug', $discussionSlug)->first()->revertTimestamp();

        return redirect('/groups/'.$groupSlug.'/discussions/'.$discussionSlug);
    }

    public function flagPost(Request $request, $group, $discussion, $postId)
    {
        if(!DiscussionPost::find($postId)->reported()->count())
        {
            ReportedPost::create([
                'postable_id' => $postId,
                'postable_type' => 'App\DiscussionPost',
                'reported_by' => $request->user()->id,
            ]);
        }
        else
        {
            ReportedPost::where('postable_id', $postId)->where('postable_type', 'App\DiscussionPost')->update(['resolved_by' => null]);
        }

        event(new DiscussionPostReported($request->user(), DiscussionThread::where('slug', $discussion)->first(), Group::slug($group)));

        return redirect(url()->previous());
    }

    public function resolve(Request $request, $group, $discussion, $postId)
    {
        DiscussionPost::find($postId)->reported()->update(['resolved_by' => $request->user()->id]);

        return redirect(url()->previous());
    }
}

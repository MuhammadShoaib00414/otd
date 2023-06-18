<?php

namespace App\Http\Controllers\Api\Groups;

use App\Group;
use App\DiscussionPost;
use App\DiscussionThread;
use App\Events\Discussions\DiscussionReplied;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    public function index($slug, Request $request)
    {
        $group = Group::where('slug', $slug)->first();

        $discussions = $group->discussions()->orderBy('id', 'desc')->get();
        $locale = $request->user()->locale;

        $discussions = $discussions->map(function($thread) use ($locale) {
            if($locale != 'en')
                $thread = $thread->localize($locale);
            $thread->owner = $thread->owner;
            $thread->owner->photo_path = $thread->owner->photo_path;
            $thread->post_count = $thread->posts()->count();

            return $thread;
        });

        return $discussions;
    }

    public function show($discussionSlug)
    {
        $discussion = DiscussionThread::where('slug', $discussionSlug)->first();

        return $discussion;
    }

    public function getPosts($slug, Request $request)
    {
        $discussion = DiscussionThread::where('slug', $slug)->first();

        $posts = tap($discussion->posts()->paginate(7))->map(function ($post) use ($request) {
            $post->post = new \stdClass();
            $post->post->content = $post->body;
            $owner = $post->owner;
            $post->post->user = $owner;
            $post->post->user_id = $owner->id;
            $post->post_at = $post->updated_at;
            $post->hasUserLiked = $post->hasUserLiked($request->user()->id);
            $post->is_reported = $post->is_reported;
            $post->likes_count = $post->likes()->count();
            $post->post_type = "App\\DiscussionPost";

            return $post;
        });

        return $posts;
    }

    public function postReply($discussionSlug, Request $request)
    {
        $validate = $request->validate([
            'body' => 'required',
        ]);

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

        $post->post = new \stdClass();
        $post->post->content = $post->body;
        $owner = $post->owner;
        $post->post->user = $owner;
        $post->post->user_id = $owner->id;
        $post->post->user->photo_path = $owner->photo_path;
        $post->post_at = $post->updated_at;
        $post->hasUserLiked = $post->hasUserLiked($request->user()->id);
        $post->is_reported = $post->is_reported;
        $post->likes_count = $post->likes()->count();
        $post->post_type = "App\\DiscussionPost";

        event(new DiscussionReplied($request->user(), $discussion, $discussion->group));

        return $post;
    }
}

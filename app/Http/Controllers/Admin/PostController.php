<?php

namespace App\Http\Controllers\Admin;

use App\ArticlePost;
use App\Group;
use App\Http\Controllers\Controller;
use App\Post;
use App\ScheduledPost;
use App\TextPost;
use App\User;
use App\Events\NewPost;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function create()
    {
    	return view('admin.posts.create');
    }

    public function store(Request $request)
    {
    	if($request->post_type == 'content')
    	{
    		$validate = $request->validate([
	            'title' => 'required|max:255',
	            'url' => 'required|url',
	            'image_url' => 'url',
	        ]);

	        try {
	            if ($request->has('custom_image_upload') && getimagesize($request->custom_image_upload) == false)
	                return redirect()->back()->with('invalid-image', 'Error: image url must be an image.');
	        } catch (\Exception $e) {
	            return redirect()->back()->with('invalid-image', 'Error: image url must be an image.');
	        }

	        if($request->has('custom_image_upload'))
	            $name = $request->custom_image_upload->store('images', 's3');
	        else {
	            $contents = file_get_contents($request->input('image'));
	            $mime = getRemoteMimeType($request->input('image'));
	            $mime = explode('/', $mime)[1];
	            $name = "images/".time().".{$mime}";
	            if(strpos($name, '?'))
	                $name = substr($name, 0, strpos($name, "?"));
	            Storage::disk('s3')->put($name, $contents);
	        }
	        
	        $articlePost = ArticlePost::create([
	            'title' => $request->input('title'),
	            'image_url' => $name,
	            'url' => $request->input('url'),
	        ]);

	        $post = Post::create([
	            'post_type' => get_class($articlePost),
	            'post_id' => $articlePost->id,
	            'post_at' => Carbon::parse($request->date . ' ' . $request->time . ' ' . $request->user()->timezone)->tz('UTC')->toDateTimeString(),
	        ]);
    	} else {
    		if(!$request->hasFile('photo')) {
	            $validation = $request->validate([
	                'message' => 'required',
	                'photo' => 'file|max:51200',
	            ]);
	        }

	        $text = linkify($request->input('message'));

	        $textPost = TextPost::create([
	            'user_id' => null,
	            'content' => $text,
	        ]);

	        $post = Post::create([
	        	'post_at' => Carbon::parse($request->date . ' ' . $request->time . ' ' . $request->user()->timezone)->tz('UTC')->toDateTimeString(),
	            'post_type' => get_class($textPost),
	            'post_id' => $textPost->id,
	        ]);

	        if($request->has('photo')) {
	            $post->update([
	                'photo_path' => $request->photo->store('text-posts', 's3'),
	            ]); 
	        }
	        event(new NewPost($request->user(), $textPost));
    	}

    	$post->groups()->attach($request->groups);

    	$scheduledPost = ScheduledPost::create([
    		'post_id' => $post->id,
    		'query' => $request->query_builder_query,
    		'user_count' => count(explode(',', $request->query_builder_users)),
		]);

		$users = explode(',', str_replace(['[', ']'], '', $request->query_builder_users));
    	if(count($users) && $users[0] != '')
    		$post->users()->sync($users);


    	if ($request->has('groups') && $request->groups) {
    		$usersTotal = User::whereHas('groups', function ($q) use ($request) {
	    		$q->whereIn('groups.id', $request->groups ?? []);
	    	});
    	}
    	if ($users) {
    		if (isset($usersTotal))
    			$usersTotal = $usersTotal->orWhereIn('users.id', $users)->count();
    		else
    			$usersTotal = User::whereIn('users.id', $users)->count();
    	}

    	$scheduledPost->update(['users_count' => $usersTotal]);

    	return redirect('/admin/posts');
    }

    public function indexScheduledPosts()
    {
    	return view('admin.posts.indexScheduled')->with([
    		'posts' => ScheduledPost::orderBy('id', 'desc')->get(),
    	]);
    }

    public function indexPosts()
    {
    	return view('admin.posts.index')->with([
    		'posts' => Post::orderBy('post_at', 'desc')->wherePostNotDeleted()->paginate(20),
    	]);
    }

    public function edit($postId)
    {
    	return view('admin.posts.edit')->with([
    		'scheduledPost' => ScheduledPost::find($postId),
    	]);
    }

    public function update(Request $request, $scheduledPostId)
    {
    	$scheduledPost = ScheduledPost::find($scheduledPostId);
    	if($request->post_type == 'content')
    	{
    		$validate = $request->validate([
	            'title' => 'required|max:255',
	            'url' => 'required|url',
	            'image_url' => 'url',
	        ]);

	        try {
	            if ($request->has('custom_image_upload') && getimagesize($request->custom_image_upload) == false)
	                return redirect()->back()->with('invalid-image', 'Error: image url must be an image.');
	        } catch (\Exception $e) {
	            return redirect()->back()->with('invalid-image', 'Error: image url must be an image.');
	        }

	        if($request->has('custom_image_upload'))
	        {  
	            $name = $request->file('custom_image_upload')->store('images', 's3');

	            $scheduledPost->post->post->update([
		            'image_url' => '/uploads/' . $name,
		        ]);
	        }
	        
	        $scheduledPost->post->post->update([
	            'title' => $request->input('title'),
	            'url' => $request->input('url'),
	        ]);

	        $scheduledPost->post->update([
	            'post_at' => Carbon::parse($request->date . ' ' . $request->time . ' ' . $request->user()->timezone)->tz('UTC')->toDateTimeString(),
	        ]);
    	}
    	else
    	{
    		if(!$request->hasFile('photo')) {
	            $validation = $request->validate([
	                'message' => 'required',
	                'photo' => 'file|max:51200',
	            ]);
	        }

	        $text = linkify($request->input('message'));

	        $scheduledPost->post->post->update([
	            'user_id' => $request->user()->id,
	            'content' => $text,
	        ]);

	        $scheduledPost->post->update([
	            'post_at' => Carbon::parse($request->date . ' ' . $request->time . ' ' . $request->user()->timezone)->tz('UTC')->toDateTimeString(),
	        ]);

	        if($request->has('photo'))
	        {
	            $scheduledPost->post->update([
	                'photo_path' => $request->photo->store('text-posts'),
	            ]); 
	        }
    	}

    	$users = explode(',', str_replace(['[', ']'], '', $request->query_builder_users));
    	if(count($users) && $users[0] != '')
    		$scheduledPost->post->users()->sync($users);

    	$scheduledPost->post->groups()->sync($request->groups);

    	$scheduledPost->update([
    		'query' => $request->query_builder_query,
    		'user_count' => count(explode(',', $request->query_builder_users)),
		]);

    	return redirect('/admin/posts');
    }
}

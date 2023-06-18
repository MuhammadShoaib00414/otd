<?php

namespace App\Http\Controllers;

use App\Like;
use App\Post;
use Carbon\Carbon;
use App\ArticlePost;
use App\Notification;
use App\ReportedPost;
use App\DiscussionPost;
use App\DiscussionThread;
use App\Events\PostReported;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PostController extends Controller
{

    public function show($postId, Request $request)
    {
        Cache::flush();
        $post = Post::where('id', $postId)->get();
        if (!$post)
            return errorView('This post no longer exists.');

        event(new \App\Events\PostViewed($request->user(), $post->first()));

        return view('posts.show')->with([
            'posts' => $post,
        ]);
    }
    
    public function edit($postId, Request $request)
    {
      
        Cache::flush();
        $post = Post::find($postId);
        $postType = $post->post_type;
        $strUrl = $post->getUrlAttribute();
        $discussion = DiscussionThread::where('id', '=', $post->post_id)->first();
        $slug = explode("/posts/",$strUrl);
       
        if ($postType == 'App\DiscussionThread') {
            return redirect("{$slug[0]}/discussions/{$discussion->slug}/edit");
        } else if($postType == 'App\Shoutout') {
            return redirect("$slug[0]/shoutouts/{$post->post_id}/edit");
        } else if($postType == 'App\ArticlePost'){
            return redirect("$slug[0]/content/{$post->id}/edit");
        } else if($postType == 'App\Event'){
            return redirect("$slug[0]/events/{$post->post_id}/edit");
        }else{
            return redirect("$slug[0]/posts/{$postId}/edit");
        }
        
    }

    public function update($postId, Request $request)
    {
        Cache::flush();
        if(!$request->hasFile('photo'))
        {
            $validation = $request->validate([
                'content' => 'required',
                'photo' => 'file|max:51200',
            ]);
        }
        $text = $request->input('content');

        $post = Post::find($postId);
        $group = $post->group;

        $textPost = $post->post;

        $textPost->update([
            'content' => $text,
        ]);
        if($request->hasFile('photo'))
        {
            $post->update([
                'photo_path' => $request->photo->store('text-posts'),
            ]);
        }

        return redirect()->route('group_home', [$group->slug]);
    }

    public function delete($postId)
    {
        $post = Post::find($postId);
        $post->post->delete();
        $post->delete();
        Notification::where('notifiable_type', '=', 'App\Post')->where('notifiable_id', '=', $postId)->delete();

        return redirect(url()->previous());
    }

    public function updateContent($postId, Request $request)
    {
        try {
            $article = Post::find($postId)->post;
            if($request->has('custom_image_upload')) {  
                $name = $request->custom_image_upload->store('/uploads/images');
                // optimizeImage(public_path() . $name, 600);
            } else {
                $name = $article->image_url;
            }
            if(isset($request->update_date)){
         
                $article = Post::where('post_type', '=', 'App\ArticlePost')
                ->where('id', '=', $postId)
                ->first();
                $article->update([
                    'post_at' =>  Carbon::now()->toDateTimeString(),
                    'image_url' => $name,
                    'title' => $request->title,
                ]);
                $article->groups()->sync($request->input('groups'));
            }else{
                $article->update([
                    'image_url' => $name,
                    'title' => $request->title,
                ]);
            }
           

          

            return redirect('/home');
        
        } catch (\Throwable $th) {
            \Log::info($th->getMessage());
            return errorView($th->getMessage());
        }
    }

    public function logClick($postId, Request $request)
    {
        $post = Post::find($postId);

        if($post)
        {
            $request->user()->logs()->create([
                'action' => 'clicked post link',
                'related_model_type' => $post->group ? 'App\Group' : null,
                'related_model_id' => $post->group ? $post->group->id : null,
                'secondary_related_model_type' => 'App\Post',
                'secondary_related_model_id' => $postId,
            ]);
        }
        return redirect()->away((is_null(parse_url($request->next, PHP_URL_HOST)) ? '//' : '').$request->next);
    }

    public function report(Request $request, $postId)
    {
        if(!Post::find($postId)->reported()->count()) {
            ReportedPost::create([
                'postable_id' => $postId,
                'postable_type' => 'App\Post',
                'reported_by' => $request->user()->id,
            ]);
            event(new PostReported(false, $postId, $request->user()->name));
        }
        else if(!Post::find($postId)->reported()->whereNull('resolved_by')->count())
        {
            Post::find($postId)->reported()->update([
                'resolved_by' => null,
            ]);
        }

        return redirect(url()->previous());
    }

    public function resolve(Request $request, $postId)
    {
        $post = Post::find($postId);
        $post->reported()->update(['resolved_by' => $request->user()->id]);
        $post->notifications()->where('email_notification_id', 7)->delete();

        return redirect(url()->previous());
    }

    public function toggleLike(Request $request)
    {
        if($request->postable_type == 'AppComments' ){
           $type =  substr_replace("AppComments","App\Comments",0);
        }
      
        if ($request->postable_type == 'App\Comments' || $request->postable_type == 'AppComments')
        {
            $type = 'App\Comments';
            $id = $request->postable_id;
        } 
        else if($request->postable_type != 'App\DiscussionPost')
        {
            $type = 'App\Post';
            $id = $request->postable_id;
        }
        else 
        {
            $type = 'App\DiscussionPost';
            $id = $request->postable_id;
        }

        if(!$request->user()->hasLiked($type, $id))
        {
            $request->user()->likes()->create([
                'postable_type' => $type,
                'postable_id' => $id,
            ]);
        }
        else
        {
            $request->user()->likes()->where('postable_type', $type)->where('postable_id', $id)->delete();
        }

        return response(200);
    }

    public function getLikes(Request $request)
    {
        $userIds = Like::where('postable_type', $request->postable_type)->where('postable_id', $request->postable_id)->pluck('user_id');
        $users = DB::table('users')->select('id', 'name', 'photo_path')->whereIn('id', $userIds)->get();
        //davis is not proud of these next few lines
        $users = $users->map(function($user) {
            return [
                    'photo_path' => $user->photo_path = getS3Url($user->photo_path),
                    'id' => $user->id,
                    'name' => $user->name,
            ];
        });

        return response()->json($users);
    }

    public function getcommentLikes(Request $request)
    {
        $userIds = Like::where('postable_type', $request->postable_type)->where('postable_id', $request->postable_id)->pluck('user_id');
        $users = DB::table('users')->select('id', 'name', 'photo_path')->whereIn('id', $userIds)->get();
        //davis is not proud of these next few lines
        $users = $users->map(function($user) {
            return [
                    'photo_path' => $user->photo_path = getS3Url($user->photo_path),
                    'id' => $user->id,
                    'name' => $user->name,
            ];
        });

        return response()->json($users);
    }
}

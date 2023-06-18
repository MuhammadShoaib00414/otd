<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Post;
use App\User;
use App\Group;
use App\Comments;
use Carbon\Carbon;
use App\Notification;
use App\ReportedPost;
use App\ReportedUsers;
use App\Events\NewComment;
use App\Events\PostReported;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;

class PostController extends Controller
{
    public function feed(Request $request)
    {
        Cache::flush();
        if ($request->has('group'))
            $posts = $this->getGroupPosts($request);
        else if($request->has('discussion'))
            return redirect('/api/discussions/'.$request->discussion.'/posts');
        else
            $posts = $this->getDashboardPosts($request);


        return response()->json($posts);
    }

    public function delete($postId)
    {
        $post = Post::find($postId);
        if(!$post)
            return;
        $post->post->delete();
        $post->delete();
        Notification::where('notifiable_type', '=', 'App\Post')->where('notifiable_id', '=', $postId)->delete();

        return response(200);
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

        return response(200);
    }

    public function resolve(Request $request, $postId)
    {
        $post = Post::find($postId);
        $post->reported()->update(['resolved_by' => $request->user()->id]);
        $post->notifications()->where('email_notification_id', 7)->delete();

        return response(200);
    }

    protected function getDashboardPosts($request)
    {
      
        Cache::flush();
        $posts = tap($request->user()->dashboard_posts)->map(function ($post) use ($request) {
            if (!$post->post) // If there is no underlying post, it may have been deleted and we can skip it.
                return;
             
            $post->is_user_admin = $post->isUserAdmin($request->user());
            $post->post->user = $post->post->user;
            $post->post = $post->post;
            $post->hasUserLiked = $post->hasUserLiked($request->user()->id);
            $created_by = DB::table('group_user')
            ->where('user_id', '=', $post->post->created_by)
            ->where('group_id', '=', $post->post->group_id)
            ->where('is_admin', '=',1)
            ->get()->count();
            $post->is_poster_group_admin = $created_by;
            $post->is_reported = $post->is_reported;
            $post->likes_count = $post->likes()->count();
            $post->comments_count = $post->comments()->count();
            if($post->posted_as_group_id)
                $post->posted_by_group = $post->posted_by_group;
            if ($post->post instanceof \App\DiscussionThread) {
                $post->post->first_post = $post->post->posts()->first();
                $post->post->recent_posts = $post->post->recent_posts;
            } else if ($post->post instanceof \App\Shoutout) {
                $post->post->load(['shouted', 'shouting']);
            } else if ($post->post instanceof \App\Event && $post->post->image_path) {
                $post->post->image_url = $post->post->image_path;
            } else if ($post->post instanceof \App\ArticlePost) {
                $post->post->image = $post->post->image_url;
                $post->post->embedded_video = $post->post->embedded_video;
                $post->post->url = $post->post->url;
                $post->post->is_video = $post->post->is_video;
            }

            return $post;
        });

        return $posts;
    }

    protected function getGroupPosts($request)
    {
        Cache::flush();
        $group = Group::where('slug', $request->group)->first();
        $ordered_post_ids = $group->ordered_post_ids;
        $blockedByMe = ReportedUsers::where('reported_by', $request->user()->id)->where('status', 'blocked')->pluck('user_id')->toArray();
        $whoBlockedMe = ReportedUsers::where('user_id', $request->user()->id)->where('status', 'blocked')->pluck('reported_by')->toArray();
        $blockedUserIds = array_merge($blockedByMe, $whoBlockedMe);
        $posts = Post::with(['post' => function ($morphTo) use ($blockedUserIds) {
                     $morphTo->morphWith([
                        \App\DiscussionThread::class => [],
                        \App\TextPost::class => [],
                        \App\Event::class => [],
                        \App\Shoutout::class => [],
                        \App\ArticlePost::class => [],
                    ]);
                }])
                ->whereHasMorph('post', [
                    \App\TextPost::class,
                    \App\Event::class,
                    \App\Shoutout::class,
                    \App\ArticlePost::class,
                    \App\DiscussionThread::class,
                ], function (Builder $query) use ($blockedUserIds) {
                        $query->whereNull('deleted_at');
                        $table = $query->getModel()->getTable(); 
                        if ($table == 'shoutouts') {
                            $query->whereNotIn('shoutout_by', $blockedUserIds);
                        } else if ($table == 'events') {
                            $query->whereNotIn('created_by', $blockedUserIds);
                        } else {
                            $query->whereNotIn('user_id', $blockedUserIds);
                        }
                })
                ->where('posts.is_enabled', 1)
                ->groupPosts($group->viewable_group_ids)
                ->where('posts.post_at', '<=', Carbon::now()->toDateTimeString())
                ->orderBy('posts.post_at', 'desc')
                ->whereNotIn('posts.post_type', $group->getDisabledContentTypes());
  

        if($ordered_post_ids && count($ordered_post_ids)) {
            $posts = $posts->orderByRaw("FIELD(id, ".implode(',', $ordered_post_ids).")");
        }

       
        if ($request->has('since'))
            $posts = $posts->where('post_at', '>=', request()->since);
       
        if($group->pinned_post_id)
            $posts = $posts->where('posts.id', '!=', $group->pinned_post_id);

        if($ordered_post_ids && !count($ordered_post_ids) < 7 * $request->page)
        $posts = $posts->whereIn('id', $ordered_post_ids);

        $posts = $posts->paginate(7);
        if($group->pinned_post_id && $group->pinned_post)
        {
            $pinned_post = $group->pinned_post;
            $pinned_post->is_pinned = true;
            // $posts = array_unshift($posts, $pinned_post);
            tap($posts,function($paginatedInstance) use ($pinned_post){
                return $paginatedInstance->getCollection()->prepend($pinned_post);
            });
        }
     
        // $posts = tap($request->user()->dashboard_posts)->map(function ($post) use ($request) {
            $posts = tap($posts)->map(function ($post) use ($request) {
            if (!$post->post) // If there is no underlying post, it may have been deleted and we can skip it.
                return;

            $post->is_user_admin = $post->isUserAdmin($request->user());
            $post->post = $post->post;
            $post->post->user = $post->post->user;
            $post->hasUserLiked = $post->hasUserLiked($request->user()->id);
            $created_by = DB::table('group_user')
            ->where('user_id', '=', $post->post->created_by)
            ->where('group_id', '=', $post->post->group_id)
            ->where('is_admin', '=',1)
            ->get()->count();
            $post->is_poster_group_admin = $created_by;
            $post->is_reported = $post->is_reported;
            $post->likes_count = $post->likes()->count();
            $post->comments_count = $post->comments()->count();
            if($post->posted_as_group_id)
                $post->posted_by_group = $post->posted_by_group;
                $post->group = $post->group;
            if ($post->post instanceof \App\DiscussionThread) {
                $post->post->first_post = $post->post->posts()->first();
                $post->post->recent_posts = $post->post->recent_posts;
                $post->group = $post->group;
            } else if ($post->post instanceof \App\Shoutout) {
                $post->post->load(['shouted', 'shouting']);
                $post->group = $post->group;
            } else if ($post->post instanceof \App\Event) {
                $post->post->image_url = $post->post->image_path;
                $post->group = $post->group;
            } else if ($post->post instanceof \App\ArticlePost) {
                $post->post->image = $post->post->image_url;
                $post->post->embedded_video = $post->post->embedded_video;
                $post->post->url = $post->post->url;
                $post->post->is_video = $post->post->is_video;
                $post->group = $post->group;
            }

            return $post;
        });

        return $posts;

    }

    public function likes(Post $post)
    {
        $likes = $post->likes()->with('user')->get();

        $users = $likes->map(function ($like) {
            return [
                    'photo_path' => $like->user->photo_path,
                    'id' => $like->user->id,
                    'name' => $like->user->name,
            ];
        });

        return $users;
    }

    public function moveUp($slug, $post)
    {
        $group = Group::slug($slug);

        $group->movePostUp($post);

        return redirect()->back();
    }

    public function moveDown($slug, $post)
    {
        $group = Group::slug($slug);

        $group->movePostDown($post);

        return redirect()->back();
    }

    public function pin($slug, $postId)
    {
        
        $pinned_post_id = Group::where('pinned_post_id', $postId)->first();
        
        if(empty($pinned_post_id)){
          
            Group::where('slug', $slug)->update([
                'pinned_post_id' => $postId,
            ]);
        }else{
           
            Group::where('slug', $slug)->update([
                'pinned_post_id' => null,
            ]);
            
        }

        return true;
    }
    public function CommentSave(Request $request)
    {
      
        $data = $request->all();
       
        $post = Post::where('id',$data['postId'])->first();

        $userComment = Comments::create([
            'post_id' => $data['postId'],
            'user_id' => \Auth::user()->id,
            'text' => $data['text'],
        ]);
        $data = array(                        
            'post' => $post,
            'comment' => $data['text'],
        );
        if ($post->post->user->email != auth()->user()->email)
            event(new NewComment($data));
        $userComment->save();
      
        return response()->json('successfully added');
    }
    public function getComment($id,Request $request)
    {
        $query = Comments::query()->where('post_id',$id);
        $query->with('user');
        $query->orderBy('created_at', 'DESC');
        $comments = $query->paginate(5);
        foreach($comments as $key => $comment){
            $comments[$key]->load('user');
            $comments[$key]->hasUserLiked = $comment->hasUserLiked();
            $comments[$key]->likes_count = $comment->likes()->count();
         }
        return $comments;      
       
    }
    public function deleteComment($commentId)
    {
      
        $comment = Comments::find($commentId);
        if(!$comment)
            return;
    
            Comments::where('id', '=', $commentId)->delete();

        return response(200);
    }
}

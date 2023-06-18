<?php

namespace App\Http\Controllers\Group;

use App\Post;
use App\Group;
use App\TextPost;
use Carbon\Carbon;
use App\ArticlePost;
use App\Notification;
use App\ReportedPost;
use App\Events\NewPost;
use App\Events\PostReported;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'group']);
    }

    public function selectType(Request $request, $slug)
    {
  
        $group = Group::where('slug', '=', $slug)->first();
        $groupName = Group::where('slug', '=', $slug)->first();


        return view('groups.posts.select-type')->with([
            'group' => $group,
            'groupName' => $groupName,
        ]);
    }
    
    public function index(Request $request, $slug) {
        $group = Group::where('slug', '=', $slug)->first();

        $request->user()->logs()->create([
            'action' => 'viewed posts',
            'related_model_type' => 'App\Group',
            'related_model_id' => $group->id,
        ]);

        $posts = $group->textPosts()->with('comments')
                       ->orderBy('post_at', 'desc');
                        
        if (!$group->isUserAdmin($request->user()->id))
            $posts->where('post_at', '<=', \Carbon\Carbon::now());
        return view('groups.posts.index')->with([
            'group' => $group,
            'posts' => $posts->get(),
        ]);
    }

    public function create(Request $request, $slug)
    {
        $group = Group::where('slug', '=', $slug)->first();

        return view('groups.posts.create')->with([
            'group' => $group,
        ]);
    }

    public function store(Request $request, $slug)
    {
       
        if(!$request->hasFile('photo')) {
            $validation = $request->validate([
                'content' => 'required',
                'photo' => 'file|max:51200',
            ]);
        }

        $text = linkify($request->input('content'));

        if($request->has('links')) {
            $custom_menu = [];
            foreach($request->links as $link) {
                if(isset($link['title']) && isset($link['url']))
                    $custom_menu[] = $link;
            }
        }

        $group = Group::where('slug', '=', $slug)->first();

        if($request->has('date') && $request->date != null && $request->has('time') && $request->time != null ) {
            $timezonedPostAt = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($request->date . ' ' . $request->time), $request->user()->timezone);
            $postAt = $timezonedPostAt->tz('UTC');
        } else {
            $postAt = Carbon::now();
        }

        $textPost = TextPost::create([
            'user_id' => $request->user()->id,
            'content' => $text,
            'custom_menu' => $request->has('links') ? $custom_menu : '',
            'localization' => $request->localization,
        ]);
        $post = Post::create([
            'post_type' => get_class($textPost),
            'post_id' => $textPost->id,
            'group_id' => $group->id,
            'post_at' => $postAt,
        ]);

        if($request->has('post_as_group'))
        {
            $post->update([
                'posted_as_group_id' => $group->id,
            ]);
        }

        if($request->has('photo'))
        {
            $path = $request->photo->store('text-posts', 'public_old');
            optimizeImage(public_path() . '/uploads/' . $path);
            $post->update([
                'photo_path' => $path,
            ]); 
        }
       // $post->groups()->attach($group->id);
        $post->groups()->attach($request->input('groups'));
        $group->resetOrderedPosts();

      
        
    

        event(new NewPost($request->user(), $textPost));

        return redirect("/groups/{$slug}");
    }

    public function delete($slug, $id, Request $request)
    {
       
        $group = Group::where('slug', '=', $slug)->first();
        $post = Post::find($id);

        $usersToDeleteNotificationsFor = $group->users;
        $otherGroups = $post->groups()->where('groups.id', '!=', $group->id)->get();
        foreach ($otherGroups as $g) {
            $usersToDeleteNotificationsFor = $usersToDeleteNotificationsFor->whereNotIn('id', $g->users()->pluck('id'));
        }
        if ($usersToDeleteNotificationsFor->count()) {
            Notification::where('notifiable_type', '=' ,'App\Post')
                        ->where('notifiable_id', '=', $id)
                        ->whereIn('user_id', $usersToDeleteNotificationsFor->pluck('id'))
                        ->delete();
        }
        if($post->group && $post->group->slug == $slug)
        {
            $post->post->delete();
            $post->delete();
        }
        else
            $post->groups()->detach($group->id);
         return redirect("home/#/groups/{$slug}");
    }

    public function report(Request $request, $slug, $postId)
    {
        if(!Post::find($postId)->reported()->count()) {
            ReportedPost::create([
                'postable_id' => $postId,
                'postable_type' => 'App\Post',
                'reported_by' => $request->user()->id,
            ]);
            event(new PostReported(Group::where('slug', $slug)->first(), $postId, $request->user()->name));
        }
        else if(!Post::find($postId)->reported()->whereNull('resolved_by')->count())
        {
            Post::find($postId)->reported()->update([
                'resolved_by' => null,
            ]);
        }

        return redirect(url()->previous());
    }

    public function resolve(Request $request, $slug, $postId)
    {
        $post = Post::find($postId);
        $post->reported()->update(['resolved_by' => $request->user()->id]);
        $post->notifications()->where('email_notification_id', 7)->delete();

        return redirect(url()->previous());
    }

    public function indexReported($slug, Request $request)
    {
        $group = Group::where('slug', $slug)->first();

        return view('groups.posts.reported')->with([
            'posts' => $group->reported_posts,
            'group' => $group,
        ]);
    }

    public function indexResolved($slug, Request $request)
    {
        $group = Group::where('slug', $slug)->first();

        return view('groups.posts.resolved')->with([
            'posts' => $group->resolved_posts,
            'group' => $group,
        ]);
    }

    public function show($slug, $postId, Request $request)
    {
        $group = Group::where('slug', $slug)->first();
        if (!$group)
            return errorView('This post no longer exists.');
        $post = Post::where('id', $postId)->get();
        if (!$post)
            return errorView('This post no longer exists.');

        if(!$post->first()->post instanceof \App\ArticlePost)
            $post->first()->post->notificationsFor($request->user()->id)->update(['viewed_at' => Carbon::now()]);

        event(new \App\Events\PostViewed($request->user(), $post->first()));

        return view('groups.posts.show')->with([
            'posts' => $post,
            'group' => $group,
        ]);
    }

    public function edit($slug, $postId)
    {
       
        $post = Post::find($postId);
        $postable = $post->post;
        $groups = getShareableGroups($post->post_type);
        if($postable instanceof \App\ArticlePost)
            return redirect("/groups/{$slug}/content/{$post->post->id}/edit");
        if($postable instanceof \App\Event)
            return redirect("/groups/{$slug}/events/{$postable->id}/edit");
        if($post->post_type == 'App\Shoutout')
            return redirect("/groups/{$slug}/shoutouts/{$postable->id}/edit");
      
      
        return view('groups.posts.edit')->with([
            'post' => $post,
            'article' => $post->post,
            'groups' => $groups,
            'group' => Group::where('slug', $slug)->first(),
        ]);

  
    }

    public function update($slug, $postId, Request $request)
    {

      if(isset($request->update_posted_date)){
            Post::where('id', $postId)->update(array('post_at' => Carbon::now()->tz('UTC')->toDateTimeString()));
       }

        if(!$request->hasFile('photo'))
        {
            $validation = $request->validate([
                'content' => 'required',
                'photo' => 'file|max:51200',
            ]);
        }

        if(isset($request->update_posted_date)){
            Post::where('id', $postId)->update(array('post_at' => Carbon::now()->toDateTimeString()));
       }

        if($request->has('links')) {
            $custom_menu = [];
            foreach($request->links as $link) {
                if(isset($link['title']) && isset($link['url']))
                    $custom_menu[] = $link;
            }
        }

        $text = linkify($request->input('content'));

        $group = Group::where('slug', '=', $slug)->first();

        $post = Post::find($postId);

       
        $textPost = $post->post;
        $timezonedPostAt = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($request->date . ' ' . $request->time), $request->user()->timezone);
     
        $postAt = $timezonedPostAt->tz('UTC');

        $textPost->update([
            // 'user_id' => $request->user()->id,
            'content' => $text,
            'post_at' => $postAt,
            'custom_menu' => $request->has('links') ? $custom_menu : '',
            'localization' => $request->localization,
        ]);
        $post->groups()->attach($request->input('groups'));
        //$post->groups()->attach($request->input('groups'));
        $group->resetOrderedPosts();
      
        if($request->has('post_as_group'))
        {
            $post->update([
                'posted_as_group_id' => $group->id,
            ]);
        }
        else
        {
            $post->update([
                'posted_as_group_id' => null,
            ]);
        }
        
        if($request->hasFile('photo'))
        {
            $post->update([
                'photo_path' => $request->photo->store('text-posts'),
            ]);
        }

      

        return redirect("/groups/{$slug}");
    }

    public function pin($groupSlug, $post)
    {
        $group = Group::where('slug', $groupSlug)->first();
        if($group->pinned_post_id == $post)
            $group->update(['pinned_post_id' => null]);
        else
            $group->update(['pinned_post_id' => $post]);

        return redirect('/groups/'.$groupSlug);
    }

    public function logClick(Group $group, $postId, Request $request)
    {
        if($postId && $postId != 'undefined')
        {
            $request->user()->logs()->create([
                'action' => 'clicked post link',
                'related_model_type' => 'App\Group',
                'related_model_id' => $group->id,
                'secondary_related_model_type' => 'App\Post',
                'secondary_related_model_id' => $postId,
            ]);
        }
        return redirect()->away((is_null(parse_url($request->next, PHP_URL_HOST)) ? '//' : '').$request->next);
    }

    public function moveUp($slug, $postId)
    {
        $group = Group::slug($slug);

        $group->movePostUp($postId);

        return redirect()->back();
    }

    public function moveDown($slug, $postId)
    {
        $group = Group::slug($slug);

        $group->movePostDown($postId);

        return redirect()->back();
    }
}

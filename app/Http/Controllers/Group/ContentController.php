<?php

namespace App\Http\Controllers\Group;

use Excel;
use App\Post;
use App\Group;
use Embed\Embed;
use Carbon\Carbon;
use App\ArticlePost;
use Illuminate\Http\Request;
use App\Exports\ContentExport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ContentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'group']);
    }

    public function index($slug, Request $request)
    {

        $group = Group::where('slug', '=', $slug)->first();

        $posts = Post::groupPosts([$group->id])
            ->where('post_type', '=', 'App\ArticlePost')
            ->where('posts.post_at', '<=', Carbon::now())
            ->whereHasMorph('post', [\App\ArticlePost::class])
            ->with(['post' => function ($morphTo) {
                $morphTo->morphWith([
                    \App\ArticlePost::class => [],
                ]);
            }])
            ->orderBy('posts.post_at', 'desc');

        $count = $posts->count();
        $posts = $posts->simplePaginate(12);

        return view('groups.articles.index')->with([
            'posts' => $posts,
            'group' => $group,
            'count' => $count,
        ]);
    }

    public function add($slug, Request $request)
    {
        $group = Group::where('slug', '=', $slug)->first();
        $groupName = Group::where('slug', '=', $slug)->first();
        $article = ArticlePost::first( );
        if(!$group->isUserAdmin($request->user()->id) && !$request->user()->is_admin && !$group->can_users_post_content)
            return redirect('/groups/'.$slug);
        return view('groups.articles.create')->with([
            'group' => $group,
            'article' => $article,
            'groupName' => $groupName
        ]);
    }

    public function store($slug, Request $request)
    {

        set_time_limit(0);
        //dd($request->get('groups'));
        $request->validate([
            'title' => 'required',
        ]);

        $info = Embed::create($request->input('url'));

        if (!$request->has('image') && !$request->has('custom_image_upload'))
            return redirect()->back()->with('invalid-image', 'Error: image required');

        try {
            if ($request->has('image') && !$request->has('custom_image_upload') && getimagesize($request->image) == false)
                return redirect()->back()->with('invalid-image', 'Error: image url must be an image.');
        } catch (\Exception $e) {
            return redirect()->back()->with('invalid-image', 'Error: image url must be an image.');
        }

        try {
            if ($request->has('custom_image_upload') && getimagesize($request->custom_image_upload) == false)
                return redirect()->back()->with('invalid-image', 'Error: image url must be an image.');
        } catch (\Exception $e) {
            return redirect()->back()->with('invalid-image', 'Error: image url must be an image.');
        }

        $group = Group::where('slug', '=', $slug)->first();

        if (!$group->isUserAdmin($request->user()->id) && !$request->user()->is_admin && !$group->can_users_post_content)
            return redirect('/groups/' . $slug);

        if ($request->has('custom_image_upload')) {
            $name = $request->custom_image_upload->store('images', 's3');
            // optimizeImage(public_path() . '/uploads/' . $name, 600); 
        } else {
            $contents = file_get_contents($request->input('image'));
            $name = "images/" . time();
            if (strpos($name, '?'))
                $name = substr($name, 0, strpos($name, "?"));
            $file = Storage::put($name, $contents);
            // optimizeImage(public_path() . '/uploads/' . $name, 1000);
        }
        $articlePost = ArticlePost::create([
            'title' => $request->input('title'),
            'image_url' => $name,
            'url' => $request->input('url') . '?rel=0',
            'code' => ($info->type == 'video') ? $info->code : null,
            'user_id' => $request->user()->id,
        ]);
        if ($request->date == null || $request->time) {
            $postdate = Carbon::now()->toDateTimeString();
        } else {
            $postdate = Carbon::parse($request->date . ' ' . $request->time . ' ' . $request->user()->timezone)->tz('UTC')->toDateTimeString();
        }


        $post = Post::create([
            'post_type' => get_class($articlePost),
            'post_id' => $articlePost->id,
            'post_at' => $postdate,
            'group_id' => $group->id,
        ]);
        $post->groups()->sync($request->input('groups'));
        // $post->groups()->attach($group->id);

        $group->resetOrderedPosts();

        foreach ($request->get('groups') as $groupId) {
            $otherGroup = Group::where('id', $groupId)->first();
            $otherGroup->resetOrderedPosts();
        }

        event(new \App\Events\NewArticle($request->user(), $articlePost));

        return redirect('/groups/' . $slug . '/content');
    }

    public function edit($slug, $articleId)
    {
        $post = Post::find($articleId);
        $groups = getShareableGroups($post->post_type);
        return view('groups.articles.edit')->with([
            'article' => ArticlePost::find($post->post_id),
            'group' => Group::slug($slug),
            'groups' => $groups,
        ]);
    }

    public function update($slug, $articleId, Request $request)
    {
        if (isset($request->update_posted_date)) {
            Post::where('post_id', $articleId)->where('post_type', 'App\ArticlePost')->update(array('post_at' => Carbon::now()->toDateTimeString()));
        }

        $article = ArticlePost::find($articleId);
        if ($request->has('custom_image_upload')) {
            $name = $request->custom_image_upload->store('images', 's3');
            // optimizeImage(public_path() . $name, 600);
        } else {
            $name = $article->image_url;
        }
        $article = Post::where('post_type', '=', 'App\ArticlePost')
            ->where('post_id', '=', $articleId)
            ->first();

        if ($request->date == null || $request->time) {
            $postdate = Carbon::now()->toDateTimeString();
        } else {
            $postdate = Carbon::parse($request->date . ' ' . $request->time . ' ' . $request->user()->timezone)->tz('UTC')->toDateTimeString();
        }
        $article->update([
            'image_url' => $name,
            'title' => $request->title,
            'post_at' => $postdate,
        ]);
        $group = Group::where('slug', '=', $slug)->first();

        $article->groups()->attach($request->input('groups'));
        $group->resetOrderedPosts();
        foreach ($request->get('groups') as $groupId) {
            $otherGroup = Group::where('id', $groupId)->first();
            $otherGroup->resetOrderedPosts();
        }


        return redirect('/groups/' . $slug . '/content');
    }


    public function export(Request $request)
    {
        $start = $request->start_date ? Carbon::parse($request->start_date) : null;
        $end = $request->end_date ? Carbon::parse($request->end_date) : null;
        return Excel::download(new ContentExport($start, $end), 'articles.xlsx');
    }

    public function log($slug, $articleId, Request $request)
    {
        $request->user()->logs()->create([
            'action' => 'clicked content',
            'related_model_type' => 'App\Group',
            'related_model_id' => Group::where('slug', $slug)->first()->id,
            'secondary_related_model_type' => 'App\ArticlePost',
            'secondary_related_model_id' => $articleId,
        ]);

        ArticlePost::find($articleId)->increment('clicks');

        return redirect($request->next);
    }
}

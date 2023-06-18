<?php

namespace App\Http\Controllers\Api;

use App\Group;
use App\Http\Controllers\Controller;
use App\Post;
use App\User;
use Illuminate\Http\Request;

class ArticlesController extends Controller
{

    public function latest(Request $request)
    {
        if ($request->has('group')) {
            $group = Group::where('slug', $request->group)->first();

            $articles = Post::where('posts.post_at', '<=', \DB::raw('DATE_SUB(curdate(), INTERVAL 2 WEEK)'))
                                 ->groupPosts($group->viewable_group_ids)
                                 ->where('post_type', '=', 'App\ArticlePost')
                                 ->whereHasMorph('post', [\App\ArticlePost::class])
                                 ->with(['post' => function ($morphTo) {
                                     $morphTo->morphWith([
                                        \App\ArticlePost::class => [],
                                    ]);
                                 }])
                                 ->orderBy('posts.post_at', 'desc')
                                 ->simplePaginate(10);
        } else {
            $articles = Post::userPosts()
                            ->whereHasMorph('post', [\App\ArticlePost::class])
                            ->with(['post' => function ($morphTo) {
                                     $morphTo->morphWith([
                                        \App\ArticlePost::class => [],
                                    ]);
                                 }])
                            ->where('post_type', '=', 'App\ArticlePost')
                            ->orderBy('posts.post_at', 'desc')
                            ->limit(6)
                            ->simplePaginate(10);
        }

        return $articles;
    }

}

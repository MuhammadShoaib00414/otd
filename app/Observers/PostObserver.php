<?php

namespace App\Observers;

use App\Post;

class PostObserver
{
    public function created(Post $post)
    {
        if($post->groups()->exists())
        {
            $post->groups->each(function($group) {
                $group->resetOrderedPosts();
            });
        }
        if($post->group()->exists())
            $post->group->resetOrderedPosts();
    }
}

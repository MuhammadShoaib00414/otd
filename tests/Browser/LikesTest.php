<?php

namespace Tests\Browser;

use App\User;
use App\Group;
use App\Setting;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Support\Str;

class LikesTest extends DuskTestCase
{
    private $group;

    private $userInGroup;

    /**
     * @group posts
     * @group text-posts
     * @group likes
     * 
     * @return void
     */
    public function testUserCanLikeTextPost()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $user2 = User::where('id', '=', $user->id)->first();
            $group = Group::first();
            $user->groups()->syncWithoutDetaching([$group->id => ['is_admin' => 0]]);
            $user2->groups()->syncWithoutDetaching([$group->id => ['is_admin' => 0]]);

            $group->update([
                'can_users_post_text' => 1,
                'is_posts_enabled' => 1,
            ]);

            Setting::where('name', 'is_likes_enabled')->update(['value' => 1]);

            $postContent = Str::random(10);
            $postContent = $postContent;

            $browser->loginAs($user)
                    ->visit('/groups/'.$group->slug.'/posts/new')
                    ->value('#content', $postContent)
                    ->press('Post')
                    ->assertSee($postContent)
                    ->loginAs($user2)
                    ->visit('/groups/'.$group->slug)
                    ->assertSee($postContent)
                    ->click('.likeButton:first-of-type')
                    ->pause(1000)
                    ->assertSee('1 like');
        });
    }

    /**
     * @group posts
     * @group text-posts
     * @group likes
     * 
     * @return void
     */
    public function testUserCanSeeWhoLikesPost()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $user2 = User::where('id', '=', $user->id)->first();
            $group = Group::first();
            $user->groups()->syncWithoutDetaching([$group->id => ['is_admin' => 0]]);
            $user2->groups()->syncWithoutDetaching([$group->id => ['is_admin' => 0]]);

            $group->update([
                'can_users_post_text' => 1,
                'is_posts_enabled' => 1,
            ]);

            Setting::where('name', 'is_likes_enabled')->update(['value' => 1]);

            $postContent = Str::random(10);
            $postContent = $postContent;

            $browser->loginAs($user)
                    ->visit('/groups/'.$group->slug.'/posts/new')
                    ->value('#content', $postContent)
                    ->press('Post')
                    ->assertSee($postContent)
                    ->loginAs($user2)
                    ->visit('/groups/'.$group->slug)
                    ->assertSee($postContent)
                    ->click('.likeButton:first-of-type')
                    ->pause(1000)
                    ->assertSee('1 like')
                    ->click('.likeCount:first-of-type')
                    ->pause(3000)
                    ->assertSee($user2->name);
        });
    }

    /**
     * @group posts
     * @group text-posts
     * @group likes
     * @group newstuff
     * 
     * @return void
     */
    public function testUserCantLikePostIfLikesTurnedOff()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $user2 = User::where('id', '=', $user->id)->first();
            $group = Group::first();
            $user->groups()->syncWithoutDetaching([$group->id => ['is_admin' => 0]]);
            $user2->groups()->syncWithoutDetaching([$group->id => ['is_admin' => 0]]);

            $group->update([
                'can_users_post_text' => 1,
                'is_posts_enabled' => 1,
            ]);

            Setting::where('name', 'is_likes_enabled')->update(['value' => 0]);

            $postContent = Str::random(10);
            $postContent = $postContent;

            $browser->loginAs($user)
                    ->visit('/groups/'.$group->slug.'/posts/new')
                    ->value('#content', $postContent)
                    ->press('Post')
                    ->assertSee($postContent)
                    ->loginAs($user2)
                    ->visit('/groups/'.$group->slug)
                    ->assertNotPresent('.likeButton');

            Setting::where('name', 'is_likes_enabled')->update(['value' => 1]);
        });
    }
}

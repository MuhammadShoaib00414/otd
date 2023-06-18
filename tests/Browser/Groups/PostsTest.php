<?php

namespace Tests\Browser\Groups;

use App\User;
use App\Group;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PostsTest extends DuskTestCase
{
    /**
     * @group group-admins
     * @group posts
     * @group text-posts
     * @group groups
     * 
     * @return void
     */
    public function testGroupAdminsCanPostAsGroup()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $group = Group::first();
            $user->groups()->syncWithoutDetaching([$group->id => ['is_admin' => 1]]);

            $postContent = Str::random(10);
            $browser->loginAs($user)
                    ->visit('/groups/'.$group->slug.'/posts/new')
                    ->value('#content', $postContent)
                    ->check('post_as_group')
                    ->press('Post')
                    ->assertSee($postContent);
        });
    }

    /**
     * @group group-admins
     * @group posts
     * @group text-posts
     * 
     * @return void
     */
    public function testUserCanPostText()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $group = Group::first();
            $user->groups()->syncWithoutDetaching([$group->id => ['is_admin' => 0]]);
            $group->update([
                'can_users_post_text' => 1,
                'is_posts_enabled' => 1,
            ]);

            $postContent = Str::random(10);
            $postContent = $postContent . ' www.google.com';

            $browser->loginAs($user)
                    ->visit('/groups/'.$group->slug.'/posts/new')
                    ->value('#content', $postContent)
                    ->press('Post')
                    ->assertSee($postContent)
                    ->assertSeeLink('www.google.com');
        });
    }

    /**
     * 
     * this one takes over a minute to complete. apologies to anyone running this test.
     * 
     * @group group-admins
     * @group posts
     * @group text-posts
     * @group takes-a-while
     * 
     * @return void
     */
    public function testGroupAdminCanSchedulePost()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $group = Group::first();
            $user->groups()->syncWithoutDetaching([$group->id => ['is_admin' => 0]]);
            $group->update([
                'can_users_post_text' => 1,
                'is_posts_enabled' => 1,
                'can_group_admins_schedule_posts' => 1,
            ]);

            $postContent = Str::random(10);

            $browser->loginAs($user)
                    ->visit('/groups/'.$group->slug.'/posts/new')
                    ->value('#content', $postContent)
                    ->value('#date', Carbon::now()->tz($user->timezone)->format('m/d/y'))
                    ->value('#time', Carbon::now()->tz($user->timezone)->addMinutes(1)->format('h:i a'))
                    ->press('Post')
                    ->pause(60005)
                    ->refresh()
                    ->assertSee($postContent);
        });
    }

    /**
     *
     * @group group-admins
     * @group posts
     * @group pinned-posts
     * @group groups
     * @return void
     */
    public function testOneSingularPinnedPostsShows()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $group = factory(Group::class)->create();
            $user->groups()->syncWithoutDetaching([$group->id => ['is_admin' => 1]]);

            $postContent = Str::random(10);
            $browser->loginAs($user)
                    ->visit('/groups/'.$group->slug.'/posts/new')
                    ->value('#content', $postContent)
                    ->press('Post')
                    ->assertSee($postContent)
                    ->click('#dropdownMenuButton')
                    ->press('Pin')
                    ->assertSee($postContent)
                    ->assertSee('Pinned');
        });
    }

    /**
     * 
     * 
     * @group group-admins
     * @group posts
     * @group text-posts
     * 
     * @return void
     */
    public function testPostEditAndDelete()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $group = Group::first();
            $user->groups()->syncWithoutDetaching([$group->id => ['is_admin' => 0]]);
            $group->update([
                'can_users_post_text' => 1,
                'is_posts_enabled' => 1,
            ]);

            $postContent = Str::random(10);
            $newPostContent = Str::random(10);

            $browser->loginAs($user)
                    ->visit('/groups/'.$group->slug.'/posts/new')
                    ->value('#content', $postContent)
                    ->press('Post')
                    ->assertSee($postContent)
                    ->click('.dropdownMenuButton:first-of-type')
                    ->click('.editButton:first-of-type')
                    ->value('#content', $newPostContent)
                    ->press('Save')
                    ->assertSee($newPostContent)
                    ->click('.dropdownMenuButton:first-of-type')
                    ->click('.deleteButton:first-of-type')
                    ->assertDontSee($newPostContent);
        });
    }
}

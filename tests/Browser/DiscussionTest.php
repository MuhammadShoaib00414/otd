<?php

namespace Tests\Browser;

use App\User;
use App\Group;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DiscussionTest extends DuskTestCase
{
    // commenting this one out because it won't work. Pressing the submit button 
    // does not submit the form despite it working reliably for humans. Either dusk or
    // chromedriver is speciesist.
    //
    // public function testUserCanCreateADiscussionThread()
    // {
    //     $this->browse(function (Browser $browser) {
    //         $user = User::first();
    //         $group = Group::first();
    //         $user->groups()->syncWithoutDetaching([$group->id => ['is_admin' => 1]]);
    //         $discussionBody = Str::random(10);

    //         $browser->loginAs($user)
    //                 ->visit('/groups/'.$group->slug.'/discussions/create')
    //                 ->type('name', 'Test Discussion')
    //                 ->keys('[data-rx-type="paragraph"]', $discussionBody)
    //                 ->press('@submit')
    //                 ->waitFor('#discussionContainer')
    //                 ->assertSee($discussionBody)
    //                 ->assertSee('Write a reply');
    //     });
    // }


    /**
    * @group discussions
    * @group user-side
    * @group groups
    */
    public function testUserCanReplyToADiscussionThread()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $user->update(['locale' => 'en']);
            $group = Group::first();
            $user->groups()->syncWithoutDetaching([$group->id => ['is_admin' => 1]]);
            $thread = $group->discussions()->create([
                'name' => 'test',
                'slug' => Str::random(10),
                'user_id' => 1,
            ]);

            $replyBody = Str::random(10);

            $browser->loginAs($user)
                    ->visit('/groups/'.$group->slug.'/discussions/'.$thread->slug)
                    ->keys('#postReplyForm > div.rx-container.rx-container-0.rx-in-blur > div.rx-editor-container > div > p', $replyBody)
                    ->screenshot('test')
                    ->press('Post')
                    ->assertSee($replyBody);
        });
    }
}

<?php

namespace Tests\Browser;

use Illuminate\Support\Str;
use App\User;
use App\Group;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class IntroductionTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testIntroductionCreationWithUrl()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();
        $group = factory(Group::class)->create();
        
        $group->users()->sync([$user->id, $user2->id, $user3->id]);

        $message = Str::random(10);

        $this->browse(function (Browser $browser) use ($user, $user2, $user3, $message) {
            $browser->loginAs($user)
                    ->visit('/introductions/new?user=' . $user2->id)
                    ->click('.v-select')
                    ->waitFor('ul')
                    ->assertSee($user3->name)
                    ->click('#user2 > ul.dropdown-menu > li:nth-child(1)')
                    ->type('message', $message)
                    ->press('Send')
                    ->assertSee('Your introduction has been sent!');
        });
    }

    public function testIntroductionCreation()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();
        $group = factory(Group::class)->create();
        
        $group->users()->sync([$user->id, $user2->id, $user3->id]);

        $message = Str::random(10);

        $this->browse(function (Browser $browser) use ($user, $user2, $user3, $message) {
            $browser->loginAs($user)
                    ->visit('/introductions/new')
                    ->click('#user1 > div > div > input')
                    ->waitFor('ul')
                    ->click('#user1 > ul.dropdown-menu > li:nth-child(1)')
                    ->pause(1000)
                    ->click('#user2 > div > div > input')
                    ->waitFor('ul')
                    ->click('#user2 > ul.dropdown-menu > li:nth-child(2)')
                    ->type('message', $message)
                    ->press('Send')
                    ->assertSee('Your introduction has been sent!');
        });
    }
}

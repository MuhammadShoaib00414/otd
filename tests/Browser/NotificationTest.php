<?php

namespace Tests\Browser;

use App\User;
use App\Group;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class NotificationTest extends DuskTestCase
{
    /**
     * @group notifications
     * @group introductions
     */
    public function testIntroductionNotifications()
    {
        $this->browse(function (Browser $browser) {
            $user = factory(User::class)->create();
            $sendToUser = factory(User::class)->create();
            //get rid of user's notifications to prevent false positives
            $sendToUser->notifications()->delete();
            $sendToUser->introductions()->delete();
            $sendToUser2 = factory(User::class)->create();

            $group = factory(Group::class)->create();
            $group->users()->attach([$user->id, $sendToUser->id, $sendToUser2->id]);
            $message = 'test';

            $browser->loginAs($user)
                    ->visit('/introductions/new?user=' . $sendToUser->id)
                    ->click('.v-select')
                    ->waitFor('ul')
                    ->assertSee($sendToUser2->name)
                    ->click('#user2 > ul.dropdown-menu > li:nth-child(1)')
                    ->type('message', $message)
                    ->press('Send')
                    ->assertPathIs('/introductions/sent');

            $browser->loginAs($sendToUser)
                    ->visit('/notifications')
                    ->assertSee('New Introduction');

            $sendToUser->introductions()->first()->delete();

            $browser->loginAs($sendToUser)
                    ->visit('/notifications')
                    ->assertDontSee('New Introduction');

            $user->delete();
            $sendToUser->delete();
            $sendToUser2->delete();
            $group->delete();
        });
    }

    /**
     * @group notifications
     * @group shoutouts
     */
    public function testShoutoutNotifications()
    {
        $this->browse(function (Browser $browser) {
            $user = factory(User::class)->create();
            $sendToUser = factory(User::class)->create();
            //get rid of user's notifications to prevent false positives
            $sendToUser->notifications()->delete();
            $sendToUser->shoutouts()->delete();

            $group = factory(Group::class)->create();
            $group->users()->attach([$user->id => ['is_admin' => 1], $sendToUser->id => ['is_admin' => 1]]);
            $message = 'test';

            $browser->loginAs($user)
                    ->visit('/groups/'.$group->slug.'/shoutouts/new')
                    ->click('.v-select')
                    ->waitFor('ul')
                    ->assertSee($sendToUser->name)
                    ->screenshot('can see')
                    ->click('ul.dropdown-menu > li:nth-child(1)')
                    ->screenshot('after see')
                    ->type('reason', $message)
                    ->press('Submit Shoutout')
                    ->assertPathIs('/groups/'.$group->slug);

            $browser->loginAs($sendToUser)
                    ->visit('/notifications')
                    ->assertSee('Shoutout From')
                    ->assertSee($user->name);

            $user->shoutouts()->delete();

            $browser->loginAs($sendToUser)
                    ->visit('/notifications')
                    ->assertDontSee('Shoutout From')
                    ->assertDontSee($user->name);

            $user->delete();
            $sendToUser->delete();
            $group->delete();
        });
    }

    /**
     * @group notifications
     * @group events
     */
    public function testEventNotifications()
    {
        $this->browse(function (Browser $browser) {
            $user = factory(User::class)->create();
            $user->update(['timezone' => 'UTC']);
            $userToNotify = factory(User::class)->create();
            //get rid of user's notifications to prevent false positives
            $userToNotify->notifications()->delete();

            $group = factory(Group::class)->create();
            $group->users()->attach([$user->id => ['is_admin' => 1], $userToNotify->id => ['is_admin' => 1]]);

            $browser->loginAs($user)
                    ->visit('/groups/'.$group->slug.'/events/new')
                    ->type('name', 'test')
                    ->type('#end_time', '11:00 PM')
                    ->screenshot('event')
                    ->press('Post New Event')
                    ->assertPathIs('/groups/'.$group->slug.'/events/*');

            $browser->loginAs($userToNotify)
                    ->visit('/notifications')
                    ->assertSee('New Event');

            $user->delete();
            $userToNotify->delete();
            $group->delete();
        });
    }

    /**
     * @group notifications
     * @group ideations
     */
    public function testGroupsInvitedFromIdeationNotifications()
    {
        $this->browse(function (Browser $browser) {
            $user = factory(User::class)->create();
            $userToNotify = factory(User::class)->create();

            $group = factory(Group::class)->create();
            $group->users()->sync([$userToNotify->id]);

            $browser->loginAs($user)
                    ->visit('/ideations/create')
                    ->type('name', 'test')
                    ->type('body', 'test')
                    ->assertSee($group->name)
                    ->check('#group'.$group->id)
                    ->press('Save & post')
                    ->assertPathIs('/ideations');

            $browser->loginAs($userToNotify)
                    ->visit('/notifications')
                    ->assertSee('New Focus Group');

            $user->delete();
            $userToNotify->delete();
            $group->delete();
        });
    }
}

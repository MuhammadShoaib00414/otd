<?php

namespace Tests\Browser\Admin;

use App\User;
use App\Group;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class InviteUsersTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @group users
     * @group invite
     * @group admin
     * @return void
     */
    public function testAdminsCanInviteUsersAsEventOnly()
    {
        $user = factory(User::class)->create();
        $user->update(['is_admin' => 1]);

        $group = factory(Group::class)->create();

        $this->browse(function (Browser $browser) use ($user, $group) {
            $browser->loginAs($user)
                    ->visit('/admin/users/invites/create')
                    ->type('emails', 'test'.Str::random(5).'@test.com')
                    ->type('custom_message', 'test')
                    ->click('#event_only')
                    ->waitForText($group->name)
                    ->check('groups[]', $group->id)
                    ->press('Send invitations')
                    ->assertPathIs('/admin/users/invites')
                    ->assertSee('1 emails');
        });
    }
}

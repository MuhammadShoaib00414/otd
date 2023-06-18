<?php

namespace Tests\Browser;

use App\Category;
use App\Group;
use App\Invitation;
use App\Skill;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UserTest extends DuskTestCase
{
    /**
     * @group users
     * @group event-only
     */
    public function testUsersCanAcceptInvitationThatIsEventOnly()
    {
        $group = Group::first();
        $invitation = Invitation::create([
                        'email' => 'test'.Str::random(5).'@test.com',
                        'custom_message' => 'test',
                        'sent_at' => Carbon::now(),
                        'hash' => Str::random(7),
                        'add_to_groups' => [$group->id],
                        'is_event_only' => 1,
                    ]);
        $this->browse(function (Browser $browser) use ($invitation, $group) {
            $browser->visit('/invite/'.$invitation->hash)
                    ->type('name', 'test')
                    ->type('password', 'password')
                    ->press('Create Account')
                    ->click('@lets-go')
                    ->assertPathIs('/onboarding')
                    ->logout();
        });
    }

    /**
     * @group users
     */
    public function testUserCanAcceptInvitationWithGroup()
    {
        $group = factory(Group::class)->create();
        $invitation = Invitation::create([
                        'email' => 'testInviteWithGroup'.Str::random(5).'@test.com',
                        'custom_message' => 'test',
                        'sent_at' => Carbon::now(),
                        'hash' => Str::random(7),
                        'add_to_groups' => [$group->id],
                        'is_event_only' => 0,
                    ]);

        $this->browse(function (Browser $browser) use ($invitation, $group) {
            $browser->visit('/invite/'.$invitation->hash)
                    ->type('name', 'test')
                    ->type('password', 'passwordtest')
                    ->press('Create Account')
                    ->assertSee('Success')
                    ->click('@lets-go')
                    ->assertPathIs('/onboarding');
        });
    }

    /**
     * @group users
     */
    public function testUsersWhoAreEventOnlyCannotAccessHomeDashboard()
    {
        $user = factory(User::class)->create();
        $user->update(['is_event_only' => 1, 'is_onboarded' => 1]);

        $group = Group::first();
        $group2 = Group::where('id', '!=', $group->id)->first();
        $user->groups()->sync([$group->id, $group2->id]);

        $this->browse(function (Browser $browser) use ($user, $group) {
            $browser->loginAs($user)
                    ->visit('/home')
                    ->assertPathIs('/groups/'.$group->slug);
        });
    }

    // public function testUsersCanEditTheirProfile()
    // {
    //     $this->browse(function (Browser $browser) {
    //         $user = factory(User::class)->create();
    //         $user->location = 'Austin, TX';
    //         $user->save();
    //         $user->groups()->attach(Group::first()->id);
    //         $newName = Str::random(10);

    //         $browser->loginAs($user)
    //                 ->visit('/profile')
    //                 ->type('name', $newName)
    //                 ->select('#gender_pronouns_select', 'He/Him/His')
    //                 ->type('twitter', Str::random(8))
    //                 ->type('instagram', Str::random(8))
    //                 ->type('facebook', Str::random(8))
    //                 ->type('linkedin', Str::random(8))
    //                 ->type('website', 'www.'.Str::random(8).'.com')
    //                 ->screenshot('test')
    //                 ->check('#groupsContainer input:nth-child(1)')
    //                 ->press('Save changes')
    //                 ->assertPathIs('/users/'.$user->id)
    //                 ->assertSee($newName);
    //     });
    // }
}

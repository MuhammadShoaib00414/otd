<?php

namespace Tests\Browser;

use App\User;
use App\Group;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class OnboardingTest extends DuskTestCase
{
    /**
     * @group onboarding
     * @group onboarding1
     */
    public function testOnboardingWizardWithMoreThanOneGroupActive()
    {
        $user = factory(User::class)->create();
        $groups = Group::all();
        foreach($groups as $group) {
            $group->delete();
        }
        factory(\App\Group::class)->create();
        factory(\App\Group::class)->create();
        factory(\App\Group::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/onboarding')
                    ->press('@next')
                    ->type('company', 'asd')
                    ->type('job_title', 'asd')
                    ->type('location', 'asd')
                    ->press('Next Step')
                    ->press('Next Step')
                    ->press('Next Step')
                    ->press('Next Step')
                    ->press('Next Step')
                    ->press('Next Step')
                    ->press('Next Step')
                    ->check("groups[]")
                    ->press('Next Step')
                    ->press('@finish')
                    ->assertPathIs('/users/'.$user->id);
        });
    }

    /**
     * @group onboarding
     * @group onboarding2
     */
    public function testOnboardingWizardWithOnlyOneGroupActive()
    {
        $user = factory(User::class)->create();

        $groups = Group::all();
        foreach($groups as $group) {
            $group->delete();
        }

        factory(\App\Group::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/onboarding')
                    ->press('@next')
                    ->type('company', 'asd')
                    ->type('job_title', 'asd')
                    ->type('location', 'asd')
                    ->press('Next Step')
                    ->press('Next Step')
                    ->press('Next Step')
                    ->press('Next Step')
                    ->press('Next Step')
                    ->press('Next Step')
                    ->press('Next Step')
                    ->press('@finish')
                    ->assertPathIs('/users/'.$user->id);
        });

        factory(\App\Group::class, 6)->create();
    }

    /**
     * @group onboarding
     * @group onboarding3
     */
    public function testOnboardingWizardWithEventOnlyUser()
    {
        $user = factory(User::class)->create();
        $user->is_event_only = 1;
        $user->save();
        $group = Group::first();
        $user->groups()->attach($group);

        $this->browse(function (Browser $browser) use ($user, $group) {
            $browser->loginAs($user)
                    ->visit('/onboarding')
                    ->press('@next')
                    ->type('company', 'asd')
                    ->type('job_title', 'asd')
                    ->type('location', 'asd')
                    ->press('Next Step')
                    ->press('Next Step')
                    ->press('Next Step')
                    ->press('Next Step')
                    ->press('Next Step')
                    ->press('Next Step')
                    ->press('Next Step')
                    ->press('@finish')
                    ->assertPathIs('/users/'.$user->id);
            $browser->visit('/home')
                    ->assertPathIs('/groups/'.$group->slug);
        });
    }
}

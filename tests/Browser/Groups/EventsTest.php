<?php

namespace Tests\Browser\Groups;

use App\User;
use App\Group;
use App\Event;
use App\Setting;
use Carbon\Carbon;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\DatabaseMigrations;


class EventsTest extends DuskTestCase
{
    protected $event = null;

    /**
     * @group events
     * @group group-admins
     * @group groups
     */
    public function testGroupAdminCanMakeEvents()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $group = Group::first();
            $user->groups()->syncWithoutDetaching([$group->id => ['is_admin' => 1]]);
            $eventTime = Carbon::now()->addDays(1);
            $eventDescription = Str::random(10);

            $browser->loginAs($user)
                    ->visit('/groups/'.$group->slug.'/events/new')
                    ->type('name', 'Test Event 1')
                    ->click('#date')
                    ->click('div[role=navigator] div:nth-child(3)')
                    ->click('.gj-picker-bootstrap tr:nth-child(3) td:nth-child(4)')
                    ->type('#time', '11:00 am')
                    ->type('event_end_time', '2:00 pm')
                    ->type('description', $eventDescription)
                    ->press('Post New Event')
                    ->assertSee($eventDescription);
        });
    }

    /**
     * @group events
     * @group rsvp
     * @group groups
     */
    public function testUserCanRSVPForEvents()
    {
        $this->browse(function (Browser $browser) {
            $event = Event::orderBy('id', 'desc')->first();
            $group = Group::first();
            $event->date = $event->date->addDays(20);
            $event->end_date = $event->end_date->addDays(20);
            $event->group_id = $group->id;
            $event->save();
            $user = factory(User::class)->create();
            $event->groups()->attach($group);
            $user->groups()->sync($group);
            $browser->loginAs($user)
                    ->visit('/groups/'.$group->slug.'/events/'.$event->id)
                    ->press("I'm going")
                    ->assertSee('attending');
        });
    }

    /**
     * @group events
     * @group rsvp
     * @group groups
     */
    public function testUserCanNotRSVPForEvents()
    {
        $this->browse(function (Browser $browser) {
            $event = Event::orderBy('id', 'desc')->first();
            $event->date = $event->date->addDays(20);
            $event->end_date = $event->end_date->addDays(20);
            $event->allow_rsvps = 0;
            $event->save();
            $user = factory(User::class)->create();
            $group = Group::first();
            $event->groups()->attach($group);
            $user->groups()->sync($group);
            $browser->loginAs($user)
                    ->visit('/groups/'.$group->slug.'/events/'.$event->id)
                    ->assertDontSee('RSVP');
        });
    }

    /**
     * @group events
     * @group feed
     * @group groups
     */
    public function testPostToGroupFeedOnAndOff()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $group = Group::first();
            $user->groups()->syncWithoutDetaching([$group->id => ['is_admin' => 1]]);
            $eventTime = Carbon::now()->addDays(1);

            $group->update([
                'can_users_post_events' => 1,
                'is_events_enabled' => 1,
            ]);

            $name = Str::random(10);
            $description = Str::random(10);

            $browser->loginAs($user)
                    ->visit('/groups/'.$group->slug.'/events/new')
                    ->type('name', $name)
                    ->click('#date')
                    ->click('div[role=navigator] div:nth-child(3)')
                    ->click('.gj-picker-bootstrap tr:nth-child(3) td:nth-child(4)')
                    ->type('#time', '11:00 am')
                    ->type('event_end_time', '2:00 pm')
                    ->type('description', $description)
                    ->assertChecked('#post_to_group_feed')
                    ->press('Post New Event')
                    ->visit('/groups/'.$group->slug)
                    ->assertSee($name)
                    ->click('.dropdownMenuButton:first-of-type')
                    ->click('.editButton:first-of-type')
                    ->uncheck('#post_to_group_feed')
                    ->screenshot('before')
                    ->press('Save changes')
                    ->visit('/groups/'.$group->slug)
                    ->assertDontSeeIn('.card', $name);
        });
    }

    /**
     * @group events
     * @group calendar
     * @group groups
     */
    public function testUserCanAddToCalendarEvents()
    {
        $this->browse(function (Browser $browser) {
            $event = Event::orderBy('id', 'desc')->first();
            $user = factory(User::class)->create();
            $group = Group::first();
            $event->update([
                'group_id' => $group->id,
            ]);
            $user->groups()->syncWithoutDetaching($group->id);

            $browser->loginAs($user)
                    ->visit('/groups/'.$group->slug.'/events/'.$event->id)
                    ->press("I'm going")
                    ->click('.addToCalendarButton')
                    ->assertSee('Google Calendar');
        });
    }

    /**
     * @group events
     * @group groups
     * 
     * function named by panic! at the disco
     */
    public function testUserHasAccessToEventWithoutAccessToGroup()
    {
        $this->browse(function (Browser $browser) {
            $user = factory(User::class)->create();
            $user->update([
                'location' => 'test',
                'job_title' => 'testing',
                'is_onboarded' => 1,
            ]);

            $eventGroup = factory(Group::class)->create();
            $eventGroup->events()->sync([]);
            $userGroup = factory(Group::class)->create();

            $user->groups()->sync([$userGroup->id]);

            $eventTime = Carbon::now()->addDays(1);
            $eventDescription = Str::random(10);

            $event = Event::create([
                'created_by' => 1,
                'group_id' => $eventGroup->id,
                'name' => Str::random(10),
                'slug' => Str::random(10),
                'date' => $eventTime->toDateTimeString(),
                'allow_rsvps' => 1,
            ]);

            $browser->loginAs($user)
                    ->visit('/groups/'.$eventGroup->slug.'/events/'.$event->id)
                    ->assertPathIs('/home');

            $user->rsvps()->create([
                'event_id' => $event->id,
                'response' => '',
            ]);

            $browser->visit('/groups/'.$eventGroup->slug.'/events/'.$event->id)
                    ->screenshot('after')
                    ->assertPathIs('/events/'.$event->id);
        });
    }


    /**
     * @group events
     * @group localization
     * @group groups
     */
    public function testCanSeeLocalizedEvent()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $group = Group::first();
            $user->groups()->syncWithoutDetaching([$group->id => ['is_admin' => 1]]);
            $eventTime = Carbon::now()->addDays(1);
            $eventDescription = Str::random(10);

            $group->update([
                'can_users_post_events' => 1,
                'is_events_enabled' => 1,
            ]);

            $user->update([
                'locale' => 'es',
            ]);

            Setting::where('name', 'is_localiation_enabled')->update([
                'value' => 1,
            ]);

            $name_es = Str::random(10);
            $description_es = Str::random(10);

            $browser->loginAs($user)
                    ->visit('/groups/'.$group->slug.'/events/new')
                    ->type('name', 'Test Event 1')
                    ->value('#name_es', $name_es)
                    ->value('#description_es', $description_es)
                    ->click('#date')
                    ->click('div[role=navigator] div:nth-child(3)')
                    ->click('.gj-picker-bootstrap tr:nth-child(3) td:nth-child(4)')
                    ->type('#time', '11:00 am')
                    ->type('event_end_time', '2:00 pm')
                    ->type('description', $eventDescription)
                    ->press('Publicar nuevo evento')
                    ->assertSee($description_es)
                    ->assertSee($name_es);

            $user->update([
                'locale' => 'en'
            ]);
        });
    }
}

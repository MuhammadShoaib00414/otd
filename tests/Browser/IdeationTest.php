<?php

namespace Tests\Browser;

use App\User;
use App\Group;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class IdeationTest extends DuskTestCase
{
    /**
     * @group ideations
     * @group create-ideation
     */
    public function testUserCanCreateIdeations()
    {
        $this->browse(function (Browser $browser) {
            $user = factory(User::class)->create();
            $user->groups()->attach(Group::first()->id);
            $user->is_admin = 1;
            $user->save();
            $ideationBody = Str::random(10);

            $browser->loginAs($user)
                    ->visit('/ideations/create')
                    ->type('name', 'First Ideation')
                    ->type('body', 'Ideation Body ' . $ideationBody)
                    ->check('groups[]')
                    ->press('Save & post')
                    ->assertPathIs('/ideations')
                    ->click('@view-ideation1')
                    ->assertSee($ideationBody);
        });
    }

    /**
     * @group ideations
     * @group reply-to-ideation
     */
    public function testUserCanReplyToIdeations()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $ideationBody = Str::random(10);

            $browser->loginAs($user)
                    ->visit('/ideations/joined')
                    ->click('tbody > tr:nth-child(1) .btn')
                    ->assertSee('Write a reply')
                    ->type('body', 'Reply ' . $ideationBody)
                    ->press('Post')
                    ->assertSee($ideationBody)
                    ->assertValue('textarea[name=body]', '');
        });
    }

    /**
     * @group ideations
     * @group ideation-survey
     */
    public function testUserCanAddSurveyWithoutImageToIdeation()
    {
         $this->browse(function (Browser $browser) {
            $user = User::first();
            $url = 'https://sweg.co';

            $browser->loginAs($user)
                    ->visit('/ideations/joined')
                    ->click('@view-ideation1')
                    ->assertSee('Write a reply')
                    ->clickLink('Surveys')
                    ->click('button[data-target="#addSurveyModal"')
                    ->assertSee('Fetch')
                    ->type('url', $url)
                    ->press('Fetch')
                    ->waitForText('sweg')
                    ->click('#submitButton')
                    ->assertSee('sweg')
                    ->assertSee('Delete');
        });
    }
}

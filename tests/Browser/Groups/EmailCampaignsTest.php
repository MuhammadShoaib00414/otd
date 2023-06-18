<?php

namespace Tests\Browser\Groups;

use App\Group;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class EmailCampaignsTest extends DuskTestCase
{
    /**
     *
     * @group group-admins
     * @group emails
     * @group email-campaigns
     * @return void
     */
    public function testGroupAdminsCanCreateEmailCampaigns()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $user->update(['locale' => 'en']);
            $group = Group::first();
            $user->groups()->syncWithoutDetaching([$group->id => ['is_admin' => 1]]);
            $group->update([
                'is_email_campaigns_enabled' => 1,
            ]);
            $emailSubject = Str::random(8);

            $browser->loginAs($user)
                    ->visit("/groups/{$group->slug}")
                    ->assertSee('Email Campaigns')
                    ->clickLink('Email Campaigns')
                    ->assertSee('New campaign')
                    ->clickLink('New campaign')
                    ->type('email_subject', $emailSubject)
                    ->press('Save')
                    ->assertSee('Email campaign has been created');
        });
    }

    /**
     *
     * @group group-admins
     * @group emails
     * @group email-campaigns
     * @return void
     */
    public function testGroupAdminsCanEditEmailCampaigns()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $group = Group::first();
            $user->update(['locale' => 'en']);
            $emailSubject = Str::random(8);

            $browser->loginAs($user)
                    ->visit("/groups/{$group->slug}")
                    ->assertSee('Email Campaigns')
                    ->clickLink('Email Campaigns')
                    ->clickLink('Edit/Send')
                    ->clickLink('Edit')
                    ->assertSee('Edit Campaign')
                    ->type('email_subject', $emailSubject)
                    ->scrollIntoView('.socicon-twitter')
                    ->press('Save changes')
                    ->waitForText('Email campaign has been saved')
                    ->assertSee('Email campaign has been saved');
        });
    }

    /**
     *
     * @group group-admins
     * @group emails
     * @group email-campaigns
     * @return void
     */
    public function testGroupAdminsCanSendEmailCampaigns()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $group = Group::first();
            $user->update(['locale' => 'en']);

            $browser->loginAs($user)
                    ->visit("/groups/{$group->slug}")
                    ->assertSee('Email Campaigns')
                    ->clickLink('Email Campaigns')
                    ->clickLink('Edit/Send')
                    ->clickLink('Send')
                    ->clickLink('Send now')
                    ->select('groups[]', $group->id)
                    ->press('Review')
                    ->assertSee('Send to groups')
                    ->press('Send')
                    ->assertSee('Campaign is being processed to be sent.');
        });
    }

}

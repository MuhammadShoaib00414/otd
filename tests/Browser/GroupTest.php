<?php

namespace Tests\Browser;

use App\Group;
use App\User;
use \App\Setting;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Illuminate\Support\Str;
use Tests\DuskTestCase;

class GroupTest extends DuskTestCase
{
    /**
    * @group groups
    * @group delete-group
    */
    public function testDeletedGroupDisappears()
    {
        $this->browse(function (Browser $browser) {
            // another one that should be separated when there's time
            $user = User::first();
            $user->update(['locale' => 'en']);
            $group = factory(Group::class)->create();
            $group->users()->syncWithoutDetaching($user->id);
            $group->update(['should_display_dashboard' => 1]);

            $browser->loginAs($user)
                    ->visit("/home")
                    ->assertSee($group->name);

            $group->delete();

            $browser->loginAs($user)
                    ->visit("/home")
                    ->assertDontSee($group->name)
                    //for good measure
                    ->visit('/notifications')
                    ->assertSee('Notifications');
        });
    }

    /**
    * @group page-names
    * @group misc-settings
    * @group localization
    * @group groups
    */
    public function testSpanishGroupPageNames()
    {
        $this->browse(function (Browser $browser) {
            // another one that should be separated when there's time
            $user = User::first();
            $user->update(['locale' => 'es']);
            $group = Group::orderBy('id', 'desc')->first();
            $group->users()->syncWithoutDetaching($user->id);
            $group->update(['localization' => ['es' => [
                'home_page_name' => 'group home page es',
                'posts_page_name' => 'posty es',
                'content_page_name' => 'contenty es',
                'calendar_page_name' => 'cali es',
                'shoutouts_page_name' => 'shouty es',
                'discussions_page_name' => 'discussy es',
            ]]]);

            $browser->loginAs($user)
                    ->visit("/groups/{$group->slug}")
                    ->assertSee('group home page es')
                    ->assertSee('posty es')
                    ->assertSee('contenty es')
                    ->assertSee('cali es')
                    ->assertSee('shouty es')
                    ->assertSee('discussy es');
        });
    }

    /**
    * @group page-names
    * @group misc-settings
    * @group why-does-this-feature-exist
    * @group groups
    */
    public function testGroupPageNames()
    {
        $this->browse(function (Browser $browser) {
            // another one that should be separated when there's time
            $user = User::where('locale', 'en')->first();
            $group = Group::orderBy('id', 'desc')->first();
            $group->users()->syncWithoutDetaching($user->id);
            $group->update([
                'home_page_name' => 'group home page',
                'posts_page_name' => 'posty',
                'content_page_name' => 'contenty',
                'calendar_page_name' => 'cali',
                'shoutouts_page_name' => 'shouty',
                'discussions_page_name' => 'discussy',
            ]);

            $browser->loginAs($user)
                    ->visit("/groups/{$group->slug}")
                    ->assertSee('group home page')
                    ->assertSee('posty')
                    ->assertSee('contenty')
                    ->assertSee('cali')
                    ->assertSee('shouty')
                    ->assertSee('discussy');
        });
    }

    /**
    * @group welcome-message
    * @group onboarding
    * @group registration
    * @group groups
    * @group join-with-code
    */
    public function testGroupAssignOnRegister()
    {
        $this->browse(function (Browser $browser) {
            $sendingUser = User::first();
            $group = factory(Group::class)->create();

            $joinWithCodeGroup = factory(Group::class)->create();
            $welcome_message = Str::random(10);
            $join_code = Str::random(15);
            $joinWithCodeGroup->update([
                'should_display_dashboard' => 1,
                'join_code' => $join_code,
                'is_welcome_message_enabled' => 1,
                'welcome_message' => $welcome_message,
                'welcome_message_sending_user_id' => $sendingUser->id,
            ]);

            $joinWithCodeGroup->users()->syncWithoutDetaching($sendingUser);

            Setting::where('name', 'open_registration')->update(['value' => 1]);
            \App\RegistrationPage::where('id', '>', 0)->delete();

            //if you look closely you can see me losing my mind
            $prompt = 'far boo far? bar foo bar!';

            $name = Str::random(7);
            $slug = Str::random(10);

            $registration_page = \App\RegistrationPage::create([
                'name' => $name,
                'slug' => $slug,
                'assign_to_groups' => ["{$group->id}"],
                'is_welcome_page_accessible' => 1,
                'prompt' => $prompt,
            ]);

            $password = Str::random(10);

            $browser->logout()
                    ->visit('/')
                    ->assertSee('Signup')
                    ->press('@signup')
                    ->type('name', 'name!')
                    ->type('email', Str::random(7) . '@gmail.com')
                    ->type('password', $password)
                    ->type('confirmPassword', $password)
                    ->type('access_code', $join_code)
                    ->press('Sign Up')
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
                    ->press('Next Step')
                    ->press('@finish')
                    ->assertSee($group->name)
                    ->assertSee($joinWithCodeGroup->name)
                    ->visit('/messages')
                    ->click('@message1')
                    ->assertSee($welcome_message)
                    ->assertSee($sendingUser->name);

            $registration_page->delete();
        });
    }


    /**
    * @group welcome-message
    * @group groups
    * @group join-with-code
    */
    public function testGroupWelcomeMessage()
    {
        $this->browse(function (Browser $browser) {
            // another one that should be separated when there's time
            $user = User::where('is_admin', 0)->where('locale', 'en')->first();
            $sendingUser = User::where('id', '!=', $user->id)->first();
            $group = Group::first();
            $group->users()->syncWithoutDetaching($sendingUser->id);
            $group->users()->detach($user->id);
            $group->update([
                'join_code' => 'foobar',
                'is_welcome_message_enabled' => 1,
                'welcome_message' => 'barfoo',
                'welcome_message_sending_user_id' => $sendingUser->id,
            ]);

            $browser->loginAs($user)
                    ->visit("/account")
                    ->type('code', 'foobar')
                    ->press('Join')
                    ->assertSee('Joined')
                    ->visit('/messages')
                    ->assertSee('barfoo')
                    ->assertSee($sendingUser->name);
        });
    }


    /**
    * @group enabled-features
    * @group live-chat
    * @group groups
    */
    public function testLiveChat()
    {
        $this->browse(function (Browser $browser) {
            // another one that should be separated when there's time
            $user = User::where('is_admin', 0)->where('locale', 'en')->first();
            $group = Group::first();
            $group->users()->syncWithoutDetaching([$user->id => ['is_admin' => 0]]);
            $group->chatRoom()->updateOrCreate([],[
                'is_enabled' => 1,
            ]);

            $browser->loginAs($user)
                    ->visit("/groups/{$group->slug}")
                    ->assertSee('Chat');

            $group->chatRoom()->delete();

            $browser->loginAs($user)
                    ->visit("/groups/{$group->slug}")
                    ->assertDontSee('Chat');
        });
    }

    /**
    * @group enabled-features
    * @group networking-lounge
    * @group groups
    */
    public function testNetworkingLounge()
    {
        $this->browse(function (Browser $browser) {
            // another one that should be separated when there's time
            $user = User::where('is_admin', 0)->where('locale', 'en')->first();
            $group = Group::first();
            $group->users()->syncWithoutDetaching([$user->id => ['is_admin' => 0]]);
            $virtualRoom = \App\VirtualRoom::create([]);

            $group->lounge()->create([
                'name' => 'Networking Lounge',
                'virtual_room_id' => $virtualRoom->id,
            ]);

            $browser->loginAs($user)
                    ->visit("/groups/{$group->slug}")
                    ->assertSee('Networking Lounge')
                    ->click('@lounge')
                    ->assertSee('Networking Lounge');

            $group->lounge()->delete();

            $browser->loginAs($user)
                    ->visit("/groups/{$group->slug}")
                    ->assertDontSee('Networking Lounge');
        });
    }

    /**
    * @group enabled-features
    * @group permissions
    * @group groups
    */
    public function testDisabledFeatures()
    {
        $this->browse(function (Browser $browser) {
            // another one that should be separated when there's time
            $user = User::where('is_admin', 0)->where('locale', 'en')->first();
            $group = Group::first();
            $group->users()->syncWithoutDetaching([$user->id => ['is_admin' => 0]]);
            // you get NOTHING! You LOSE! Good day sir!
            $group->update([
                'is_files_enabled' => 0,
                'is_posts_enabled' => 0,
                'is_shoutouts_enabled' => 0,
                'is_discussions_enabled' => 0,
                'is_events_enabled' => 0,
                'is_content_enabled' => 0,
                'is_budgets_enabled' => 0,
                'is_sequence_enabled' => 0,
            ]);

            $browser->loginAs($user)
                    ->visit("/groups/{$group->slug}")
                    ->assertDontSee('New Post')
                    ->assertDontSee('Budgets')
                    ->assertDontSee('Learning Modules')
                    ->assertDontSee($group->files_alias ?: 'Files');
        });
    }


    /**
    * @group group-users
    * @group permissions
    * @group groups
    */
    public function testStandardUserPermissionsInGroup()
    {
        $this->browse(function (Browser $browser) {
            // another one that should be separated when there's time
            $user = User::where('is_admin', 0)->where('locale', 'en')->first();
            $group = Group::first();
            $group->users()->syncWithoutDetaching([$user->id => ['is_admin' => 0]]);
            $group->update([
                'can_users_upload_files' => 1,
                'can_users_post_text' => 1,
                'can_users_post_shoutouts' => 1,
                'can_users_post_events' => 1,
                'can_users_post_discussions' => 1,
                'can_users_post_content' => 1,
                'is_files_enabled' => 1,
                'is_posts_enabled' => 1,
                'is_shoutouts_enabled' => 1,
                'is_discussions_enabled' => 1,
                'is_events_enabled' => 1,
                'is_content_enabled' => 1,
            ]);

            $browser->loginAs($user)
                    ->visit("/groups/{$group->slug}/posts/select-type")
                    ->assertSee($group->discussions_page_name ?: 'Discussions')
                    ->assertSee($group->posts_page_name ?: 'Posts')
                    ->assertSee($group->calendar_page_name ?: 'Calendar')
                    ->assertSee($group->shoutouts_page_name ?: 'Shoutouts')
                    ->assertSee($group->content_page_name ?: 'Content')
                    ->assertSee($group->files_alias ?: 'Files')
                    ->click('@files')
                    ->assertSee("New " . $group->files_alias ?: 'Files');
        });
    }


    /**
    * @group group-admins
    * @group permissions
    * @group groups
    */
    public function testGroupAdminPermissions()
    {
        $this->browse(function (Browser $browser) {
            // this test is like bronchitis: it should probably be separated
            // into different tests, but ain't nobody got time fo dat.
            $user = User::where('is_admin', 1)->where('locale', 'en')->first();
            $group = Group::first();
            $group->users()->syncWithoutDetaching($user->id);
            $group->update([
                'is_email_campaigns_enabled' => 1,
                'can_ga_toggle_content_types' => 1,
                'is_reporting_enabled' => 1,
                'is_reporting_user_data_enabled' => 1,
                'can_ga_set_live_chat' => 1,
                'can_group_admins_invite_other_groups_to_events' => 1,
                'can_group_admins_schedule_posts' => 1,
                'can_ga_order_posts' => 1,
            ]);

            $browser->loginAs($user)
                    ->visit("/groups/{$group->slug}")
                    ->assertSee('Email Campaigns')
                    ->visit("/groups/{$group->slug}/edit")
                    ->pause(2000)
                    ->assertSee('Live chat')
                    ->click('#permissionsNav')
                    ->pause(2000)
                    ->assertSee('Allow users to...')
                    ->assertSee('Activity')
                    ->assertSee('Reports')
                    ->visit("/groups/{$group->slug}/posts/new")
                    ->assertSee('Schedule To Post On');
        });
    }

    /**
    * @group private-groups
    * @group groups
    */
    public function testPrivateGroupNotJoinable()
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('is_admin', 0)->where('locale', 'en')->first();
            $group = Group::first();
            $group->users()->detach($user->id);
            $group->update([
                'is_private' => 1,
            ]);

            $browser->loginAs($user)
                    ->visit("/profile")
                    ->assertDontSee($group->name);
        });
    }

    /**
    * @group join-code
    * @group groups
    */
    public function testGroupJoinCode()
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('is_admin', 0)->first();
            $user->update([
                'is_onboarded' => 1,
            ]);
            $group = Group::first();
            $group->users()->detach($user->id);
            $group->update([
                'join_code' => 'this is a join code',
                'should_display_dashboard' => 1,
            ]);

            //because cached pivot tables are funky
            $user->touch();

            $browser->loginAs($user)
                    ->visit("/account")
                    ->type('code', 'this is a join code')
                    ->press('Join')
                    ->assertSee('Joined')
                    ->assertSee('successfully')
                    ->visit('/home')
                    ->assertSee($group->name);
        });
    }

    /**
    * @group localization
    * @group groups
    */
    public function testLocalizedGroupHeader()
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('is_admin', 1)->first();
            Setting::where('name', 'is_localization_enabled')->update(['value' => 1]);
            $user->update(['locale' => 'es']);
            $group = Group::first();
            $group->users()->syncWithoutDetaching($user->id);
            $group->update([
                'localization' => ['es' => ['dashboard_header' => 'espan header']],
                'should_display_dashboard' => 1,
            ]);

            $browser->loginAs($user)
                    ->visit("/home")
                    ->assertSee('ESPAN HEADER');
        });
    }

    /**
    * @group localization
    * @group groups
    */
    public function testLocalizedGroupName()
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('is_admin', 1)->first();
            Setting::where('name', 'is_localization_enabled')->update(['value' => 1]);
            $user->update(['locale' => 'es']);
            $group = Group::first();
            $group->users()->syncWithoutDetaching($user->id);
            $group->update([
                'localization' => ['es' => ['name' => 'espan']],
                'should_display_dashboard' => 1,
            ]);

            $browser->loginAs($user)
                    ->visit("/home")
                    ->assertSee('espan');
        });
    }

    /**
    * @group activity
    * @group group-activity
    * @group groups
    */
    public function testActivityShowsCalendar()
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('is_admin', 1)->first();
            $user->update(['locale' => 'en']);
            $group = Group::first();
            $group->users()->syncWithoutDetaching($user->id);

            $browser->loginAs($user)
                    ->visit("/groups/{$group->slug}/calendar")
                    ->visit("/groups/{$group->slug}/activity")
                    ->assertSee('viewed calendar')
                    ->visit("/groups/{$group->slug}/activity/viewed-calendar")
                    ->assertSee($user->name);
        });
    }

    /**
    * @group files
    * @group groups
    *
    * There isn't really any way to test uploading files with dusk, but I would feel guilty
    * if I left files out of testing. And I don't want to hurt files' feelings.
    * 
    * Not after last time.
    * 
    */
    public function testCanSeeFilesPage()
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('is_admin', 1)->first();
            $user->update(['locale' => 'en']);
            $group = Group::first();
            $group->users()->syncWithoutDetaching($user->id);

            $browser->loginAs($user)
                    ->visit("/groups/{$group->slug}/files")
                    ->assertSee('New');
        });
    }

    /**
    * @group admins
    * @group groups
    * @group group-members
    *
    */
    public function testCanAddUsersByEmail()
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('is_admin', 1)->first();
            $otherUser = User::where('id', '!=', $user->id)->first();
            $group = Group::first();

            if($otherUser->groups()->where('id', $group->id)->exists())
                $otherUser->groups()->detach($group->id);

            $browser->loginAs($user)
                    ->visit("/admin/groups/{$group->id}/users")
                    ->type('users', $otherUser->email)
                    ->press('Add')
                    ->assertSee('1 users added to group');
        });
    }

    /**
    * @group admins
    * @group groups
    * @group group-members
    *
    */
    public function testCanRemoveUserFromGroup()
    {

        $this->browse(function (Browser $browser) {
            $user = User::where('is_admin', 1)->first();
            $otherUser = User::where('id', '!=', $user->id)->first();
            $group = Group::first();
            $otherUser->groups()->syncWithoutDetaching([$group->id => ['is_admin' => 0]]);

            $browser->loginAs($user)
                    ->visit("/admin/users/{$otherUser->id}/groups")
                    ->uncheck("#group{$group->id}")
                    ->press('Save changes')
                    ->assertNotChecked("#group{$group->id}");
        });
    }

    /**
    * @group admins
    * @group groups
    * @group group-admins
    *
    */
    public function testCanAdminRevokeGroupAdmin()
    {

        $this->browse(function (Browser $browser) {
            $user = User::where('is_admin', 1)->first();
            $otherUser = User::where('id', '!=', $user->id)->first();
            $group = Group::first();
            $otherUser->groups()->syncWithoutDetaching([$group->id => ['is_admin' => 1]]);

            $browser->loginAs($user)
                    ->visit("/admin/users/{$otherUser->id}/groups")
                    ->uncheck("#admin{$group->id}")
                    ->press('Save changes')
                    ->assertNotChecked("#admin{$group->id}");
        });
    }

    /**
    * @group admins
    * @group groups
    * @group group-admins
    *
    */
    public function testCanMakeUserGroupAdmin()
    {

        $this->browse(function (Browser $browser) {
            $user = User::where('is_admin', 1)->first();
            $otherUser = User::where('id', '!=', $user->id)->first();
            $group = Group::first();
            $otherUser->groups()->syncWithoutDetaching([$group->id => ['is_admin' => 0]]);

            $browser->loginAs($user)
                    ->visit("/admin/users/{$otherUser->id}/groups")
                    ->check("#admin{$group->id}")
                    ->press('Save changes')
                    ->assertChecked("#admin{$group->id}");
        });
    }


    /**
    * @group groups
    * @group groupTest
    * @group group-admins
    *
    */
    public function testGroupAdminsCanAddSocialLinks()
    {

        $this->browse(function (Browser $browser) {
            $user = User::first();
            $group = Group::first();
            $user->groups()->syncWithoutDetaching([$group->id => ['is_admin' => 1]]);

            $browser->loginAs($user)
                    ->visit('/groups/'.$group->slug)
                    ->clickLink('Settings')
                    ->clickLink('Social')
                    ->waitFor('#social')
                    ->type('twitter_handle', 'austinwoman')
                    ->type('instagram_handle', 'austinwoman')
                    ->type('facebook_url', 'https://facebook.com/austinwoman')
                    ->type('linkedin_url', 'https://www.linkedin.com/company/austin-woman-magazine/')
                    ->type('website_url', 'https://atxwoman.com')
                    ->press('Save')
                    ->assertVisible('.socicon-twitter')
                    ->assertVisible('.socicon-instagram')
                    ->assertVisible('.socicon-facebook')
                    ->assertVisible('.socicon-linkedin')
                    ->assertVisible('.icon-link');
        });
    }

    /**
    * @group groups
    * @group group-members
    * @group admin
    * @group admins
    * @group add-users-by-group
    *
    */
    public function testCanAdminAddUsersToGroupByGroup()
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('is_admin', 1)->first();
            $group = Group::first();

            $browser->loginAs($user)
                    ->visit("/admin/groups/{$group->id}/users/bulk-add")
                    ->press('Add By Group')
                    ->waitFor('#byGroup')
                    ->click("#byGroup > div > div:nth-child(2) > div.form-check > input[type=checkbox]")
                    ->press('Save')
                    ->assertSee('Users added');
;                    
        });
    }

    /**
    * @group groups
    * @group group-members
    * @group admin
    * @group admins
    *
    */
    public function testCanAdminBulkAddUsersToGroup()
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('is_admin', 1)->first();
            $group = factory(Group::class)->create();
            $otherUser = User::where('id', '!=', $user->id)->first();

            $browser->loginAs($user)
                    ->visit("/admin/groups/{$group->id}/users/bulk-add")
                    ->press('Select Users')
                    ->waitFor("#user{$otherUser->id}")
                    ->click("#user{$otherUser->id}")
                    ->press('Save')
                    ->assertSee('1 Users added');

            $group->delete();
;                    
        });
    }

    /**
    * @group groups
    * @group group-members
    *
    */
    public function testUserCanSeeGroupMember()
    {

        $this->browse(function (Browser $browser) {
            $user = User::first();
            $otherUser = User::where('id', '!=', $user->id)->where('is_enabled', 1)->where('is_hidden', 0)->first();
            $group = Group::first();
            $group->users()->sync([$user->id, $otherUser->id]);

            $browser->loginAs($user)
                    ->visit("/groups/{$group->slug}/members")
                    ->assertSee($otherUser->name);
        });
    }

    /**
    * @group groups
    * @group admin
    * @group admins
    * @group user-groups
    *
    */
    public function testAdminsCanAddUsersToGroup()
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('is_admin', 1)->first();

            $otherUser = User::where('id', '!=', $user->id)->first();

            $new_group = factory(Group::class)->create();

            $browser->loginAs($user)
                    ->visit("/admin/users/{$otherUser->id}/groups")
                    ->check("#group{$new_group->id}")
                    ->press('Save changes')
                    ->assertChecked("#group{$new_group->id}");
        });
    }

    /**
    * @group groups
    * @group admin
    * @group admins
    * @group new-group
    *
    */
    public function testAdminCanCreateNewGroup()
    {

        $this->browse(function (Browser $browser) {
            $user = User::where('is_admin', 1)->first();

            $group_name = Str::random(7);

            $browser->loginAs($user)
                    ->visit('/admin/groups/create')
                    ->type('name', $group_name)
                    ->click('button[data-id="group_admin"]')
                    ->click('#form > div:nth-child(4) > div > div > div.inner.show > ul > li:nth-child(2)')
                    ->press('Create group')
                    ->assertSee($group_name);
        });
    }

    /**
     * @group groups
     * @group sort-groups
     * 
     * God only knows how to assert order of elements. Please update this counter as suited.
     * 
     * total_hours_wasted = 2;
     * 
     * */
    // public function testSortedGroups()
    // {
    //     $this->browse(function (Browser $browser) {
    //         $user = User::first();
    //         $groups = Group::limit(3)->whereNotNull('name')->get();
    //         $user->groups()->syncWithoutDetaching($groups->pluck('id'));
    //         $order_key = 1;
    //         foreach($user->groups as $group);
    //         {
    //             $group->update(['order_key' => $order_key]);
    //             $order_key++;
    //         }

    //         $browser->loginAs($user)
    //                 ->visit('/home')
    //                 ->screenshot('huh');

    //         $matches = collect($browser->elements('.group'));
    //         $uniqueIds = collect([]);

    //         $matches = $matches->filter(function($match) use ($uniqueIds) {
    //             if($uniqueIds->contains($match->getAttribute('id')))
    //                 return false;

    //             $uniqueIds->push($match->getAttribute('id'));

    //             return true;
    //         });

    //         try {
    //             foreach ($matches as $index => $domElement) {
    //                 $browser->assertAttribute('#'.$domElement->getAttribute('id'), 'body', trim($domElement->getText()));
    //             }
    //         } catch (PHPUnit_Framework_ExpectationFailedException $e) {
    //             $this->fail('Failed asserting that the element at index ' . $index . ' contains the string "' . $contents[$index] . '"');
    //         }
    //     });
    // }


}

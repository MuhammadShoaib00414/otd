<?php

namespace Tests\Browser;

use App\User;
use Facebook\WebDriver\WebDriverKeys;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class MessagesTest extends DuskTestCase
{
    public function testUserCanSendMessages()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $sendToUser = User::orderBy('id', 'desc')->first();
            $group = $user->groups()->first();
            $sendToUser->groups()->attach($group);
            $message = Str::random(10);

            $browser->loginAs($user)
                    ->visit('/messages')
                    ->clickLink('New')
                    ->click('.multiselect')
                    ->driver->getKeyboard()->sendKeys($sendToUser->name[0]);
            $browser->waitFor('.multiselect li:nth-child(2)')
                    ->click('.multiselect li:nth-child(2)')
                    ->type('message', $message)
                    ->pause(1000)
                    ->press('Send')
                    ->assertSee('Reply');
        });
    }

    public function testUserCanReplyToMessages()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $sendToUser = User::orderBy('id', 'desc')->first();
            $message = Str::random(10);

            $browser->loginAs($user)
                    ->visit('/messages')
                    ->click('.card a:nth-child(1)')
                    ->assertSee('Reply')
                    ->type('message', $message)
                    ->press('Send')
                    ->assertSee('Reply')
                    ->assertValue('textarea[name=message]', '');
        });
    }

    public function testSendingMessageFromLink()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $this->browse(function (Browser $browser) use ($user, $user2) {
            $browser->loginAs($user)
                    ->visit('/messages/new?user='.$user2->id)
                    ->type('message', 'test')
                    ->click('#sendButton')
                    ->assertPathIsNot('/messages/new');
        });
    }

}

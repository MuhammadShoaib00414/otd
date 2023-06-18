<?php

use App\User;
use App\PushNotification;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePushNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('push_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title');
            $table->string('body');
            $table->boolean('is_enabled')->default(1);
            $table->json('tags');
        });

        $push_notifications = [
            [
                'name' => 'New Message',
                'title' => 'New Message ',
                'body' => 'From @sender',
                'tags' => [
                    '@sender' => 'The user who sent the message.',
                ],
            ],
            [
                'name' => 'New Introduction',
                'title' => 'New Introduction',
                'body' => '@introducedBy has introduced you to @introducedTo',
                'tags' => [
                    '@introducedBy' => 'The user who created the introduction.',
                    '@introducedTo' => 'The other introducee',
                ],
            ],
            [
                'name' => 'New Shoutout',
                'title' => 'New Shoutout',
                'body' => 'From @shouter',
                'tags' => [
                    '@shouter' => 'User who shouted this person out.',
                ],
            ],
            [
                'name' => 'Moved Off Waitlist',
                'title' => 'Moved Off Waitlist',
                'body' => 'For @eventName',
                'tags' => [
                    '@eventName' => 'Name of the event.',
                ],
            ],
            [
                'name' => 'Event Cancelled',
                'title' => 'Event Cancelled',
                'body' => '@eventName has been cancelled.',
                'tags' => [
                    '@eventName' => 'Name of the event.',
                ],
            ],
            [
                'name' => 'Post Reported',
                'title' => 'Post Reported ',
                'body' => '@reporter\'s post was reported in @groupName',
                'tags' => [
                    '@reporter' => 'Person who created the post that was reported.',
                ],
            ],
            [
                'name' => 'New Event',
                'title' => 'New Event',
                'body' => '"@eventName"',
                'tags' => [
                    '@eventName' => 'Name of the event.',
                ],
            ],
            [
                'name' => 'Focus Group Invitation',
                'title' => 'Focus Group Invitation',
                'body' => 'You\'ve been invited to @focusGroup',
                'tags' => [
                    '@focusGroup' => 'Name of the focus group.',
                ],
            ],
            [
                'name' => 'New Discussion',
                'title' => 'New Discussion',
                'body' => '"@discussionName"',
                'tags' => [
                    '@discussionName' => 'Name of this discussion.',
                ],
            ],
            [
                'name' => 'New Discussion Reply',
                'title' => 'New Discussion Reply',
                'body' => 'From @replier',
                'tags' => [
                    '@replier' => 'Person who posted the reply.',
                ],
            ],
            [
                'name' => 'New Post',
                'title' => 'New Post',
                'body' => 'In @groupName',
                'tags' => [
                    '@groupName' => 'Name of the group.',
                ],
            ],
        ];

        foreach($push_notifications as $push_notification)
            PushNotification::create($push_notification);

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('enable_push_notifications')->default(1);
        });

        User::whereIn('notification_frequency', ['weekly', 'never'])->update([
            'notification_frequency' => 'daily',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('push_notifications');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('enable_push_notifications');
        });
    }
}

<?php

use App\PushNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBodyToPushNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('push_notifications', function (Blueprint $table) {

            $push_notifications = [
                [
                    'id' => '1',
                    'title' =>'New Message ',
                    'body' => '@sender just sent you a message!',
                    'tags' => [
                        '@sender' => 'The user who sent the message.',
                    ],
                ],
                [
                    'id' => '2',
                    'title' =>'New Introduction',
                    'body' => '@introducedBy has introduced you to @introducedTo',
                    'tags' => [
                        '@introducedBy' => 'The user who created the introduction.',
                        '@introducedTo' => 'The other introducee',
                    ],
                ],
                [
                    'id' => '3',
                    'title' =>'New Shoutout',
                    'body' => '@shouter just added a shoutout in @groupName group! Check it out and share your support.',
                    'tags' => [
                        '@shouter' => 'User who shouted this person out.',
                        '@groupName' => 'Name of the group.',
                    ],

                ],
                [
                    'id' => '6',
                    'title' =>'Post Reported',
                    'body' => '@reporter\'s post was reported in @groupName',
                    'tags' => [
                        '@reporter' => 'Person who created the post that was reported.',
                        '@groupName' => 'Name of the group.',
                    ],
                ],
                [
                    'id' => '7',
                    'title' =>'New Event',
                    'body' => '@userName just posted a new event in "@eventName" group! RSVP and don\'t miss out!',
                    'tags' => [
                        '@userName'    => 'The user who created the new Event.',
                        '@eventName' => 'Name of the event.'

                    ],
                ],
                [
                    'id' => '8',
                    'title' => 'Focus Group Invitation',
                    'body' => '@userName invites you to a new @focusGroup !',
                    'tags' => [
                        '@userName' => 'The user who invites the focus Group.',
                        '@focusGroup' => 'Name of the focus group.',
                    ],
                ],
                [
                    'id' => '9',
                    'title' => 'New Discussion',
                    'body' => '@userName just started a new discussion in "@discussionName" group! Join in and share your thoughts.',
                    'tags' => [
                        '@userName' => 'The user who created the new discussion.',
                        '@discussionName' => 'Name of this discussion.'
                    ],
                ],
                [
                    'id' => '10',
                    'title' => 'New Discussion Reply',
                    'body' => '@replier replied to your dicusstion post',
                    'tags' => [
                        '@replier' => 'Person who posted the reply.',
                    ],
                ],
                [
                    'id' => '11',
                    'title' => 'New Post',
                    'body' => ' @userName just added a new text post in @groupName group! ',
                    'tags' => [
                        '@userName' => 'The user who created the new post.',
                        '@groupName' => 'Name of the group'
                    ],

                ],
                [
                    'id' => '12',
                    'title' => 'New Content Post',
                    'body' => '@poster just added a new content post in @groupName group!',
                    'tags' => [
                        '@poster' => 'The user who sent the Comment.',
                        '@groupName' => 'Name of the group.'
                    ],
                ],
                [
                    'id' => '13',
                    'title' => 'New Comment',
                    'body' => '@userName just commented on the @groupName post!',
                    'tags' => [
                        '@userName' => 'The user who sent the Comment.',
                        '@groupName' => 'Name of the group.'
                    ],
                ],
            ];

            foreach ($push_notifications as $push_notification) {
                DB::table('push_notifications')->where('id', $push_notification['id'])
                    ->update([
                        'title' => $push_notification['title'],
                        'body' => $push_notification['body'],
                        'tags' => $push_notification['tags'],
                    ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('push_notifications', function (Blueprint $table) {
            //  Schema::dropIfExists('push_notifications');
        });
    }
}

<?php

use App\Point;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SyncPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Point::where('key', 'join')->count())
        {
            DB::table('points')->insert([
                'key' => 'join',
                'name' => 'Signing Up',
                'value' => '1',
                'description' => 'Awarded for signup',
            ]);
        }
        if(!Point::where('key', 'conversation')->count())
        {
            DB::table('points')->insert([
                'key' => 'conversation',
                'name' => 'Starting a conversation',
                'value' => '1',
                'description' => 'Awarded when a conversation (one message sent and received) with a user is started.',
            ]);
        }
        if(!Point::where('key', 'introduction')->count())
        {
            DB::table('points')->insert([
                'key' => 'introduction',
                'name' => 'Introduction',
                'value' => '5',
                'description' => 'Awarded when a user makes an introduction',
            ]);
        }
        if(!Point::where('key', 'successful-introduction')->count())
        {
            DB::table('points')->insert([
                'key' => 'successful-introduction',
                'name' => 'Successful Introduction',
                'value' => '25',
                'description' => 'Awarded when two introduced users message each other.',
            ]);
        }
        if(!Point::where('key', 'upload-photo')->count())
        {
            DB::table('points')->insert([
                'key' => 'upload-photo',
                'name' => 'Add profile photo',
                'value' => '1',
                'description' => 'Awarded when a user uploads a profile photo',
            ]);
        }
        if(!Point::where('key', 'rsvp-event')->count())
        {
            DB::table('points')->insert([
                'key' => 'rsvp-event',
                'name' => 'RSVP to an event',
                'value' => '5',
                'description' => 'Awarded for RSVPing to an event',
            ]);
        }
        if(!Point::where('key', 'find-mentor')->count())
        {
            DB::table('points')->insert([
                'key' => 'find-mentor',
                'name' => 'Find a mentor',
                'value' => '30',
                'description' => 'Awarded for finding a mentor',
            ]);
        }
        if(!Point::where('key', 'weekly-signon-1x')->count())
        {
            DB::table('points')->insert([
                'key' => 'weekly-signon-1x',
                'name' => '1x Weekly Signon',
                'value' => '2',
                'description' => 'Awarded each week that a users signs on.',
            ]);
        }
        if(!Point::where('key', 'weekly-signon-5x')->count())
        {
            DB::table('points')->insert([
                'key' => 'weekly-signon-5x',
                'name' => '5x Weekly Signon',
                'value' => '5',
                'description' => 'Awarded each week that a users signs on 5 times.',
            ]);
        }
        if(!Point::where('key', 'make-shoutout')->count())
        {
            DB::table('points')->insert([
                'key' => 'make-shoutout',
                'name' => 'Shoutout a Colleague',
                'value' => '2',
                'description' => 'Awarded when a user creates a shoutout',
            ]);
        }
        if(!Point::where('key', 'receive-shoutout')->count())
        {
            DB::table('points')->insert([
                'key' => 'receive-shoutout',
                'name' => 'Receive Shoutout',
                'value' => '10',
                'description' => 'Awarded when a user is given a shoutout',
            ]);
        }
        if(!Point::where('key', 'create-ideation')->count())
        {
            DB::table('points')->insert([
                'key' => 'create-ideation',
                'name' => 'Create Ideation',
                'value' => '5',
                'description' => 'Awarded when an a user creates an ideation.',
            ]);
        }
        if(!Point::where('key', 'view-ideation')->count())
        {
            DB::table('points')->insert([
                'key' => 'view-ideation',
                'name' => 'View Ideation',
                'value' => '1',
                'description' => 'Awarded when an a user views an ideation.',
            ]);
        }
        if(!Point::where('key', 'ideation-reply')->count())
        {
            DB::table('points')->insert([
                'key' => 'ideation-reply',
                'name' => 'Reply to Ideation',
                'value' => '1',
                'description' => 'Awarded when an a user replies to an ideation.',
            ]);
        }
        if(!Point::where('key', 'ideation-invite')->count())
        {
            DB::table('points')->insert([
                'key' => 'ideation-invite',
                'name' => 'Invite to Ideation',
                'value' => '1',
                'description' => 'Awarded when an a user invites another user to an ideation.',
            ]);
        }
        if(!Point::where('key', 'create-discussion')->count())
        {
            DB::table('points')->insert([
                'key' => 'create-discussion',
                'name' => 'Create a Discussion',
                'value' => '1',
                'description' => 'Awarded when an a user creates a discussion.',
            ]);
        }
        if(!Point::where('key', 'discussion-reply')->count())
        {
            DB::table('points')->insert([
                'key' => 'discussion-reply',
                'name' => 'Reply to a Discussion',
                'value' => '1',
                'description' => 'Awarded when an a user replies to a discussion.',
            ]);
        }
        if(!Point::where('key', 'add-expense')->count())
        {
            DB::table('points')->insert([
                'key' => 'add-expense',
                'name' => 'Add an expense',
                'value' => '1',
                'description' => 'Awarded when an a user adds an expense to a budget.',
            ]);
        }
        if(!Point::where('key', 'view-budget')->count())
        {
            DB::table('points')->insert([
                'key' => 'view-budget',
                'name' => 'View a Budget',
                'value' => '1',
                'description' => 'Awarded when an a user views a budget.',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //NA
    }
}

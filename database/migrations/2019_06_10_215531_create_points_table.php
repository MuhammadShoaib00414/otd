<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('points', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key');
            $table->string('name');
            $table->string('description')->description();
            $table->integer('value')->default(0);
            $table->timestamps();
        });

        \App\Point::insert([
            [
                'key'         => 'join',
                'name'        => 'Signing Up',
                'description' => 'Awarded on signup.',
                'value'       => 1
            ],
            [
                'key'         => 'conversation',
                'name'        => 'Starting a conversation',
                'description' => 'Awarded when a conversation (one message sent and received) with a user is started.',
                'value'       => 1
            ],
            [
                'key'         => 'introduction',
                'name'        => 'Introduction',
                'description' => 'Awarded when a user makes an introduction',
                'value'       => 5
            ],
            [
                'key'         => 'successful-introduction',
                'name'        => 'Successful Introduction',
                'description' => 'Awarded when two introduced users message each other.',
                'value'       => 25
            ],
            [
                'key'         => 'upload-photo',
                'name'        => 'Add profile photo',
                'description' => 'Awarded when a user uploads a profile photo',
                'value'       => 1
            ],
            [
                'key'         => 'rsvp-event',
                'name'        => 'RSVP to an event',
                'description' => 'Awarded for RSVPing to an event',
                'value'       => 5
            ],
            [
                'key'         => 'find-mentor',
                'name'        => 'Find a mentor',
                'description' => 'Awarded for finding a mentor',
                'value'       => 30
            ],
            [
                'key'         => 'weekly-signon-1x',
                'name'        => '1x Weekly Signon',
                'description' => 'Awarded each week that a users signs on.',
                'value'       => 5
            ],
            [
                'key'         => 'weekly-signon-5x',
                'name'        => '5x Weekly Signon',
                'description' => 'Awarded each week that a users signs on 5 times.',
                'value'       => 5
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('points');
    }
}

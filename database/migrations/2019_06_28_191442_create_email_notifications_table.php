<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('subject')->nullable();
            $table->longText('body')->nullable();
            $table->boolean('is_enabled')->default(1);
            $table->boolean('is_editable')->default(1);
            $table->timestamps();
        });

        \App\EmailNotification::insert([
            ['name' => 'On Sign Up', 'subject' => 'Welcome to On The Dot Connects!', 'description' => 'Email sent to user immediately after signing up for the first time.', 'body' => "<p>@name,</p><p>Welcome to the platform designed to help you network and connect with others that look like you from your organization.</p><p>Let us know if there's anything we can do to help you feel more comfortable and at-home here at On The Dot Connects!</p><p>Kindly,</p><p>The OTD Connects team.<br></p>", 'is_editable' => 0],
            ['name' => 'New Message Received', 'subject' => 'You have a new message', 'description' => 'Email sent to user notifying they have received a message.', 'body' => '<p align="center"><b><span style="font-size: 18px;">New Message</span></b><br></p><p align="center">A new message is waiting for you at On The Dot Connects.</p><p align="center">@cta<br></p>', 'is_editable' => 1],
            ['name' => 'New Introduction Received', 'subject' => 'You have a new introduction', 'description' => 'Email sent to user notifying they have received an introduction.', 'body' => '<p align="center"><b><span style="font-size: 18px;">New Introduction</span></b><br></p><p align="center">You\'ve been introduced to someone on On The Dot Connects!<br></p><div align="center">@cta</div>', 'is_editable' => 1],
            ['name' => 'User Received Shoutout', 'subject' => "You've been shouted-out!", 'description' => 'Email sent to user notifying another user has shouted them out in their group.', 'body' => '<p align="center"><b><span style="font-size: 18px;">New Shout Out</span></b><br></p><p align="center">Someone submitted a shout out about you on On The Dot Connects!<br></p><div align="center">@cta</div>', 'is_editable' => 1],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_notifications');
    }
}

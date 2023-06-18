<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBadgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description');
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        Schema::create('badge_user', function (Blueprint $table) {
            $table->integer('badge_id')->unsigned();
            $table->foreign('badge_id')->references('id')->on('badges');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });

        DB::insert("INSERT INTO `badges` (`id`, `name`, `description`, `icon`, `created_at`, `updated_at`) VALUES
            ('1', 'Connector', 'Sucessfully make 1 introduction.', '/images/icons8-share.svg', '2019-05-14 20:12:04', '2019-05-14 20:12:04'),
            ('2', 'Influencer', 'Successfully make 5 introductions.', '/images/icons8-share.svg', '2019-05-14 20:12:04', '2019-05-14 20:12:04'),
            ('3', 'Starter', 'Completely fill out your profile', '/images/icons8-briefcase.svg', '2019-05-14 20:12:04', '2019-05-14 20:12:04'),
            ('4', 'Communicator', 'Message at least 5 people.', '/images/icons8-speech-bubble.svg', '2019-05-14 20:12:04', '2019-05-14 20:12:04'),
            ('5', 'Mentor', 'This badge appears when a user indicates that they\'re willing to be a mentor.', '/images/icons8-mentor.png', NULL, NULL);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('badge_user');
        Schema::dropIfExists('badges');
    }
}

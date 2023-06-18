<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIntroductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('introductions', function (Blueprint $table) {
            $table->increments('id');
            $table->text('message')->nullable();
            $table->integer('sent_by')->unsigned();
            $table->foreign('sent_by')->references('id')->on('users');
            $table->timestamps();
        });

        Schema::create('introduction_user', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('introduction_id')->unsigned();
            $table->foreign('introduction_id')->references('id')->on('introductions');
            $table->boolean('is_unread')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('introduction_user');
        Schema::dropIfExists('introductions');
    }
}

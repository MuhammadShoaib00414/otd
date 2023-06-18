<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotificationsToSequencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sequence_reminders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('subject');
            $table->longText('html');
            $table->longText('template');
            $table->unsignedInteger('send_after_days');
            $table->boolean('is_enabled')->default(0);
            $table->foreignId('sequence_id');
            $table->foreign('sequence_id')->references('id')->on('sequences');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sequence_reminders');
    }
}

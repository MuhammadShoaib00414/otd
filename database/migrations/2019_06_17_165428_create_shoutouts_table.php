<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShoutoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shoutouts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('shoutout_by')->unsigned();
            $table->foreign('shoutout_by')->references('id')->on('users');
            $table->integer('shoutout_to')->unsigned();
            $table->foreign('shoutout_to')->references('id')->on('users');
            $table->text('body');
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
        Schema::dropIfExists('shoutouts');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feeds', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('type');
            $table->string('url');
            $table->timestamp('last_processed_at')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('feed_group', function (Blueprint $table) {
            $table->integer('feed_id')->unsigned();
            $table->foreign('feed_id')->references('id')->on('feeds');
            $table->integer('group_id')->unsigned();
            $table->foreign('group_id')->references('id')->on('groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feed_group');
        Schema::dropIfExists('feeds');
    }
}

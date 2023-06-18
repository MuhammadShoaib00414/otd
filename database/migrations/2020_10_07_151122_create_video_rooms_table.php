<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->boolean('is_enabled')->default(1);
            $table->string('attachable_type')->nullable();
            $table->integer('attachable_id')->nullable();
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
        Schema::dropIfExists('video_rooms');
    }
}

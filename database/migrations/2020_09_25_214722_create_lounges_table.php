<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoungesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('virtual_rooms', function (Blueprint $table) {
            $table->integer('group_id')->unsigned()->nullable()->change();
        });
        Schema::create('lounges', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('group_id')->unsigned();
            $table->foreign('group_id')->references('id')->on('groups');
            $table->bigInteger('virtual_room_id')->unsigned();
            $table->foreign('virtual_room_id')->references('id')->on('virtual_rooms');
            $table->boolean('is_enabled')->default(1);
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
        Schema::dropIfExists('lounges');
    }
}

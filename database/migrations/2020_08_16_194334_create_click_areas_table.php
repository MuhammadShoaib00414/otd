<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClickAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('click_areas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('width')->default('10');
            $table->string('height')->default('10');
            $table->string('x_coor')->default('2%');
            $table->string('y_coor')->default('2%');
            $table->text('target_url')->nullable();
            $table->bigInteger('virtual_room_id')->unsigned();
            $table->foreign('virtual_room_id')->references('id')->on('virtual_rooms');
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
        Schema::dropIfExists('click_areas');
    }
}

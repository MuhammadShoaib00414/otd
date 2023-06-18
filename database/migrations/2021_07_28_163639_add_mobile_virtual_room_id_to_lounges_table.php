<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMobileVirtualRoomIdToLoungesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lounges', function (Blueprint $table) {
            $table->integer('mobile_virtual_room_id')->nullable()->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lounges', function (Blueprint $table) {
            $table->dropColumn('mobile_virtual_room_id');
        });
    }
}

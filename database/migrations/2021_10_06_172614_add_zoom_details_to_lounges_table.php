<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddZoomDetailsToLoungesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lounges', function (Blueprint $table) {
            $table->string('zoom_invite_link')->nullable();
            $table->string('zoom_meeting_id')->nullable();
            $table->string('zoom_meeting_password')->nullable();
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
            $table->dropColumn('zoom_invite_link');
            $table->dropColumn('zoom_meeting_password');
            $table->dropColumn('zoom_meeting_id');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddZoomInviteLinkToGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->string('zoom_invite_link')->nullable();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->string('zoom_invite_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('zoom_invite_link');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('zoom_invite_link');
        });
    }
}

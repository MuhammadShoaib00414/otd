<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGroupIdToInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->integer('group_id')->nullable()->unsigned();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_event_only')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropColumn('group_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_event_only');
        });
    }
}

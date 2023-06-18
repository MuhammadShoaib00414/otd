<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpiresAtToInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->date('expires_at')->nullable();
        });
        Schema::table('group_user', function (Blueprint $table) {
            $table->date('expires_at')->nullable();
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
            $table->dropColumn('expires_at');
        });
        Schema::table('group_user', function (Blueprint $table) {
            $table->dropColumn('expires_at');
        });
    }
}

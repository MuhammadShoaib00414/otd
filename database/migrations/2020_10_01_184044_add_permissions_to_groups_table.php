<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPermissionsToGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->boolean('can_users_post_events')->default(0);
            $table->boolean('can_users_post_shoutouts')->default(0);
            $table->boolean('can_users_post_text')->default(0);
            $table->boolean('can_users_post_content')->default(0);
            $table->boolean('can_users_upload_files')->default(0);
            $table->boolean('can_users_invite')->default(0);
            $table->boolean('can_users_message_group')->default(0);
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
            $table->dropColumn('can_users_post_events');
            $table->dropColumn('can_users_post_shoutouts');
            $table->dropColumn('can_users_post_text');
            $table->dropColumn('can_users_post_content');
            $table->dropColumn('can_users_upload_files');
            $table->dropColumn('can_users_invite');
            $table->dropColumn('can_users_message_group');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEnabledContentToGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->boolean('is_content_enabled')->default(1);
            $table->boolean('is_posts_enabled')->default(1);
            $table->boolean('is_events_enabled')->default(1);
            $table->boolean('can_ga_toggle_content_types')->default(0);
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
            $table->dropColumn('is_content_enabled');
            $table->dropColumn('is_posts_enabled');
            $table->dropColumn('is_events_enabled');
            $table->dropColumn('can_ga_toggle_content_types');
        });
    }
}

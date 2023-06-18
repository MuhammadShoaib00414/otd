<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomTabsToGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->string('home_page_name')->nullable();
            $table->string('posts_page_name')->nullable();
            $table->string('content_page_name')->nullable();
            $table->string('calendar_page_name')->nullable();
            $table->string('shoutouts_page_name')->nullable();
            $table->string('discussions_page_name')->nullable();
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
            $table->dropColumn('home_page_name');
            $table->dropColumn('posts_page_name');
            $table->dropColumn('content_page_name');
            $table->dropColumn('calendar_page_name');
            $table->dropColumn('shoutouts_page_name');
            $table->dropColumn('discussions_page_name');
        });
    }
}

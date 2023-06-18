<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameGlobalFeedGroupsColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups', function ($table) {
            $table->renameColumn('publish_to_global_feed', 'publish_to_parent_feed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('groups', function ($table) {
            $table->renameColumn('publish_to_parent_feed', 'publish_to_global_feed');
        });
    }
}

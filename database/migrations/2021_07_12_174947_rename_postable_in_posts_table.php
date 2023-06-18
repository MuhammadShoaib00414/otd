<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePostableInPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            if(Schema::hasColumn('posts', 'postable_type'))
            {
                $table->renameColumn('postable_type', 'post_type');
                $table->renameColumn('postable_id', 'post_id');
            }
        });

        Schema::table('group_post', function (Blueprint $table) {
            if(Schema::hasColumn('group_post', 'order_key'))
                $table->dropColumn('order_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            if(Schema::hasColumn('posts', 'postable_type'))
            {
                $table->renameColumn('post_type', 'postable_type');
                $table->renameColumn('post_id', 'postable_id');
            }
        });
    }
}

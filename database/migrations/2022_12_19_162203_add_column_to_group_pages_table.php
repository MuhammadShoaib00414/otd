<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToGroupPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_pages', function (Blueprint $table) {
            $table->string('displayed_show')->nullable()->after('slug');
            $table->json('show_in_groups')->nullable()->after('displayed_show');
            $table->integer('is_active')->nullable()->after('show_in_groups');
            $table->integer('visibility')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_pages', function (Blueprint $table) {
            $table->dropColumn('displayed_show');
            $table->dropColumn('show_in_groups');
            $table->dropColumn('is_active');
            $table->dropColumn('visibility');
        });
    }
}

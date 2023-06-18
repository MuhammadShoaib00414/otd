<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEnabledColumnsToGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->boolean('is_budgets_enabled')->default(1);
            $table->boolean('is_files_enabled')->default(1);
            $table->boolean('is_discussions_enabled')->default(1);
            $table->boolean('is_shoutouts_enabled')->default(1);
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
            $table->dropColumn('is_budgets_enabled');
            $table->dropColumn('is_files_enabled');
            $table->dropColumn('is_discussions_enabled');
            $table->dropColumn('is_shoutouts_enabled');
        });
    }
}

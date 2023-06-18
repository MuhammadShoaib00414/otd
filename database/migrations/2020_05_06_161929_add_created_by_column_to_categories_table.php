<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatedByColumnToCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->integer('created_by')->unsigned()->nullable();
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->integer('created_by')->unsigned()->nullable();
        });

        Schema::table('keywords', function (Blueprint $table) {
            $table->integer('created_by')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });

        Schema::table('keywords', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
    }
}

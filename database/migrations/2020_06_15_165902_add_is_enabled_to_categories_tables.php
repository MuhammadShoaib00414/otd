<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsEnabledToCategoriesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('is_enabled')->default(1);
        });
        Schema::table('keywords', function (Blueprint $table) {
            $table->boolean('is_enabled')->default(1);
        });
        Schema::table('skills', function (Blueprint $table) {
            $table->boolean('is_enabled')->default(1);
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
            $table->dropColumn('is_enabled');
        });
        Schema::table('keywords', function (Blueprint $table) {
            $table->dropColumn('is_enabled');
        });
        Schema::table('skills', function (Blueprint $table) {
            $table->dropColumn('is_enabled');
        });
    }
}

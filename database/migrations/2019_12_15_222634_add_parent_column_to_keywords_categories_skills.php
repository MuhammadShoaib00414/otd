<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParentColumnToKeywordsCategoriesSkills extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('keywords', function (Blueprint $table) {
            $table->string('parent')->nullable();
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->string('parent')->nullable();
        });
        Schema::table('skills', function (Blueprint $table) {
            $table->string('parent')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('keywords', function (Blueprint $table) {
            $table->dropColumn('parent');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('parent');
        });
        Schema::table('skills', function (Blueprint $table) {
            $table->dropColumn('parent');
        });
    }
}

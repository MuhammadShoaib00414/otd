<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomMenuColumnToTextpostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('text_posts', function (Blueprint $table) {
            $table->json('custom_menu')->nullable();
            $table->json('localization')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('text_posts', function (Blueprint $table) {
            $table->dropColumn('custom_menu');
            $table->dropColumn('localization');
        });
    }
}

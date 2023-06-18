<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsBadgeColumnToTaxonomiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('taxonomies', function (Blueprint $table) {
            $table->boolean('is_badge')->default(0);
        });

        Schema::table('options', function (Blueprint $table) {
            $table->string('icon_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('taxonomies', function (Blueprint $table) {
            $table->dropColumn('is_badge');
        });

        Schema::table('options', function (Blueprint $table) {
            $table->dropColumn('icon_url');
        });
    }
}

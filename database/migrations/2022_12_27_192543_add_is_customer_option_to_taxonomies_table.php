<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsCustomerOptionToTaxonomiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('taxonomies', function (Blueprint $table) {
            $table->tinyInteger('is_customer_option')->default('1')->unsigned();

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
            $table->dropColumn('is_customer_option');
        });
    }
}

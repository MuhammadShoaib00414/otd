<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderKeyColumnToOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('options', function (Blueprint $table) {
            $table->string('profile_order_key')->nullable();
            $table->string('browse_order_key')->nullable();
            $table->string('mentor_order_key')->nullable();
        });

        Schema::table('taxonomies', function (Blueprint $table) {
            $table->string('profile_order_key')->nullable();
            $table->string('browse_order_key')->nullable();
            $table->string('mentor_order_key')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('options', function (Blueprint $table) {
            $table->dropColumn('profile_order_key');
            $table->dropColumn('browse_order_key');
            $table->dropColumn('mentor_order_key');
        });

        Schema::table('taxonomies', function (Blueprint $table) {
            $table->dropColumn('profile_order_key');
            $table->dropColumn('browse_order_key');
            $table->dropColumn('mentor_order_key');
        });
    }
}

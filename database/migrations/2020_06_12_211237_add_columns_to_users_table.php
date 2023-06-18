<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_student')->nullable();
            $table->boolean('is_unemployed_seeking')->nullable();
            $table->boolean('is_unemployed_not_seeking')->nullable();
            $table->boolean('is_retired')->nullable();
            $table->string('education')->nullable();
            $table->boolean('products_b2c')->nullable();
            $table->boolean('products_b2b')->nullable();
            $table->boolean('services_b2c')->nullable();
            $table->boolean('services_b2b')->nullable();
            $table->boolean('consultant_b2b')->nullable();
            $table->boolean('consultant_b2c')->nullable();
            $table->boolean('manages_people')->nullable();
            $table->string('position')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_student');
            $table->dropColumn('is_unemployed_seeking');
            $table->dropColumn('is_unemployed_not_seeking');
            $table->dropColumn('is_retired');
            $table->dropColumn('education');
            $table->dropColumn('products_b2c');
            $table->dropColumn('products_b2b');
            $table->dropColumn('services_b2c');
            $table->dropColumn('services_b2b');
            $table->dropColumn('consultant_b2b');
            $table->dropColumn('consultant_b2c');
            $table->dropColumn('manages_people');
            $table->dropColumn('position');
        });
    }
}

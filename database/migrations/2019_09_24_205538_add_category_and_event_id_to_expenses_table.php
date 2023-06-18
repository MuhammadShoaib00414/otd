<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoryAndEventIdToExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->unsignedInteger('expense_category_id')->nullable();
            $table->bigInteger('event_id')->unsigned()->nullable();
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->foreign('expense_category_id')->references('id')->on('expense_categories');
            $table->foreign('event_id')->references('id')->on('events');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['expense_category_id']);
            $table->dropForeign(['event_id']);
            $table->dropColumn(['expense_category_id', 'event_id']);
        });
    }
}

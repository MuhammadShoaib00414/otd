<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpenseCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        \App\ExpenseCategory::insert([
            ['name' => 'Meals'],
            ['name' => 'Entertainment'],
            ['name' => 'Venue/Rentals'],
            ['name' => 'Printing'],
            ['name' => 'Travel'],
            ['name' => 'Technology'],
            ['name' => 'Community Outreach (Donation)'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expense_categories');
    }
}

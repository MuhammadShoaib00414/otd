<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('price')->unsigned()->default(0);
            $table->json('add_to_groups')->nullable();
            $table->integer('registration_page_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('registration_pages', function (Blueprint $table) {
            $table->json('addons')->nullable();
            $table->json('coupon_codes')->nullable();
        });

        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned();
            $table->integer('ticket_id')->unsigned();
            $table->json('details')->nullable();
            $table->json('access_granted')->nullable();
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');

        Schema::table('registration_pages', function (Blueprint $table) {
            $table->dropColumn('coupon_codes');
            $table->dropColumn('addons');
        });

        Schema::dropIfExists('receipts');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurchasedColumnsToRegistrationPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registration_pages', function (Blueprint $table) {
            $table->string('purchased_warning_title')->default('You have already registered for this meeting.');
            $table->string('purchased_warning_message')->default('You can still purchase add-ons.');
            $table->string('purchased_warning_url')->nullable();
            $table->string('purchased_warning_button_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('registration_pages', function (Blueprint $table) {
            $table->dropColumn('purchased_warning_title');
            $table->dropColumn('purchased_warning_message');
            $table->dropColumn('purchased_warning_url');
            $table->dropColumn('purchased_warning_button_text');
        });
    }
}

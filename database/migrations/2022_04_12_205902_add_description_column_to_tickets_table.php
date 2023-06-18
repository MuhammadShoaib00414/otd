<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescriptionColumnToTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->text('description')->nullable();
        });

        Schema::table('registration_pages', function (Blueprint $table) {
            $table->string('ticket_prompt')->default('Select what type of access you would like.');
            $table->string('addon_prompt')->default('Select which addons you would like.');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('registration_pages', function (Blueprint $table) {
            $table->dropColumn('ticket_prompt');
            $table->dropColumn('addon_prompt');
        });
    }
}

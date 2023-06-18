<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageUrlColumnToRegistrationPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registration_pages', function (Blueprint $table) {
            $table->string('image_url')->nullable();
            $table->string('prompt');
            $table->dateTime('event_date')->nullable();
            $table->string('event_name')->nullable();
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
            $table->dropColumn('image_url');
            $table->dropColumn('prompt');
        });
    }
}

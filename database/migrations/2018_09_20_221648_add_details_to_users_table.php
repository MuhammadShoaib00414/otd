<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDetailsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('photo_path')->nullable();

            $table->string('job_title')->nullable();
            $table->text('summary')->nullable();
            $table->string('company')->nullable();
            $table->string('location')->nullable();

            $table->string('twitter')->nullable();
            $table->string('instagram')->nullable();
            $table->string('facebook')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('website')->nullable();
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
            $table->dropColumn([
                'photo_path',
                'job_title',
                'summary',
                'company',
                'location',
                'twitter',
                'instagram',
                'facebook',
                'linkedin',
                'website',
            ]);
        });
    }
}

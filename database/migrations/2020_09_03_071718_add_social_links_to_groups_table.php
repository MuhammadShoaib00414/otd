<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSocialLinksToGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->string('facebook_url')->nullable();
            $table->string('instagram_handle')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('twitter_handle')->nullable();
            $table->string('website_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn([
                'facebook_url',
                'instagram_handle',
                'linkedin_url',
                'twitter_handle',
                'website_url',
            ]);
        });
    }
}

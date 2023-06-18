<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomizableBannerToGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->string('banner_cta_title')->default('Have any questions?');
            $table->string('banner_cta_paragraph')->default('Just ask!');
            $table->string('banner_cta_button')->default('Message an admin');
            $table->string('banner_cta_url')->nullable();
        });

        $groups = DB::table('groups')->select(['name'])->get();

        foreach($groups as $group)
        {
            DB::table('groups')->where('name', $group->name)->update([
                'banner_cta_paragraph' => 'Send in any articles you think ' . $group->name . ' would benefit knowing about!',
            ]);
        }
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('banner_cta_title');
            $table->dropColumn('banner_cta_paragraph');
            $table->dropColumn('banner_cta_button');
            $table->dropColumn('banner_cta_url');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSettingsFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('settings')->insert([
            ['name' => 'short_bio_text',
             'value' => 'My superpower is...'],
            ['name' => 'questions_prompt_description',
             'value' => ''],
         ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('settings')->whereIn('name', [
            'short_bio_text',
        ])->delete();
    }
}

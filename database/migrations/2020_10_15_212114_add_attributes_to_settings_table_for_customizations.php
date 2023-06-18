<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddAttributesToSettingsTableForCustomizations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('settings')->insert([
            ['name' => 'show_mentoring_question_in_profile',
             'value' => 'true'],

            ['name' => 'is_skills_enabled',
             'value' => 'true'],
            ['name' => 'skills_title',
             'value' => 'Skillsets'],
            ['name' => 'skills_description',
             'value' => 'What are your skillsets?'],

            ['name' => 'is_categories_enabled',
             'value' => 'true'],
            ['name' => 'categories_title',
             'value' => 'Hustles'],
            ['name' => 'categories_description',
             'value' => "What's your hustle?"],

            ['name' => 'is_keywords_enabled',
             'value' => 'true'],
            ['name' => 'keywords_title',
             'value' => 'Interests/Passions'],
            ['name' => 'keywords_description',
             'value' => "What gets you up in the morning?"],
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
            'show_mentoring_question_in_profile',
            'is_skills_enabled',
            'skills_title',
            'skills_description',
            'is_categories_enabled',
            'categories_title',
            'categories_description',
            'is_keywords_enabled',
            'keywords_title',
            'keywords_description',
        ])->delete();
    }
}

<?php

use App\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertOnboardingSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $onboarding_settings = [
            'embed_video' => '',
            'intro' => [
                'title' => 'Welcome!',
                'prompt' => 'YOU MADE IT!',
                'description' => getsetting('onboarding_popup'),
            ],
            'basic' => [
                'title' => 'Fill out your profile',
                'prompt' => 'Get started by filling out just a few basic details',
            ],
            'imagebio' => [
                'title' => 'Make it personal',
                'prompt' => 'Tell us about yourself',
            ],
            'about' => [
                'title' => 'Tell others about yourself',
                'prompt' => 'About You',
            ],
            'questions' => [
                'prompt' => getsetting('question_prompt'),
                'description' => getsetting('questions_prompt_description'),
            ],
        ];

        Setting::create(['name' => 'onboarding_settings', 'value' => json_encode($onboarding_settings)]);

        Setting::create(['name' => 'enable_gender_pronouns', 'value' => 1]);

        Schema::table('questions', function (Blueprint $table) {
            $table->boolean('is_required')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Setting::where('name', 'onboarding_settings')->delete();
        Setting::where('name', 'enable_gender_pronouns')->delete();

        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('is_required');
        });
    }
}

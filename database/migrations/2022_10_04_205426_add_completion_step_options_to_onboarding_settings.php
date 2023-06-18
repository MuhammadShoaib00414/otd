<?php

use App\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompletionStepOptionsToOnboardingSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $settings = json_decode(getsetting('onboarding_settings'), true);

        $settings['completed'] = [
            'header' => "You're Done!",
            'subhead' => "You've completed your profile and are now ready to go.",
            "button" => "Let's Go",
        ];
        $settings['intro']['active'] = true;
        $settings['embed_video_active'] = true;
        $settings['basic']['active'] = true;
        $settings['imagebio']['active'] = true;
        $settings['about']['active'] = true;
        $settings['questions']['active'] = true;
        $settings['notifications']['active'] = true;
        $settings['groups']['active'] = true;
        Setting::where('name', 'onboarding_settings')->update([
            'value' => $settings,
        ]);
        \Cache::clear();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}

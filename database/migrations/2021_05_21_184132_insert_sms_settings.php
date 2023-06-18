<?php

use App\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertSmsSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $settings = [
            'is_discussion_sms_notifications_enabled',
            'is_post_sms_notifications_enabled',
            'is_event_sms_notifications_enabled',
            'is_ideation_sms_notifications_enabled',
            'is_introduction_sms_notifications_enabled',
            'is_message_sms_notifications_enabled',
            'is_shoutout_sms_notifications_enabled',
        ];

        foreach($settings as $setting)
        {
            Setting::create([
                'name' => $setting,
                'value' => 1,
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
        $settings = [
            'is_discussion_sms_notifications_enabled',
            'is_post_sms_notifications_enabled',
            'is_event_sms_notifications_enabled',
            'is_ideation_sms_notifications_enabled',
            'is_introduction_sms_notifications_enabled',
            'is_message_sms_notifications_enabled',
            'is_shoutout_sms_notifications_enabled',
        ];

        Setting::whereIn('name', $settings)->delete();
    }
}

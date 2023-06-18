<?php

use App\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGdprCheckboxLabelRowToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Setting::create(['name' => 'gdpr_prompt', 'value' => 'Would you like to make your profile visible to others on the platform for networking and messaging purposes?']);
        Setting::create(['name' => 'gdpr_checkbox_label', 'value' => 'Make my profile visible to others']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Setting::whereIn('name', ['gdpr_prompt', 'gdpr_checkbox_label'])->delete();
    }
}

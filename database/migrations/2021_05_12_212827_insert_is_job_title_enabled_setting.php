<?php

use App\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertIsJobTitleEnabledSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Setting::create([
            'name' => 'is_job_title_enabled',
            'value' => '1',
        ]);

        Setting::create([
            'name' => 'is_company_enabled',
            'value' => '1',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Setting::whereIn('name', ['is_company_enabled', 'is_job_title_enabled'])->delete();
    }
}

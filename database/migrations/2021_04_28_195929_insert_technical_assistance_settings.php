<?php

use App\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertTechnicalAssistanceSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Setting::create([
            'name' => 'is_technical_assistance_link_enabled',
            'value' => '0',
        ]);

        Setting::create([
            'name' => 'technical_assistance_email',
            'value' => '',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Setting::where('name', 'is_technical_assistance_link_enabled')->orWhere('name', 'technical_assistance_email')->delete();
    }
}

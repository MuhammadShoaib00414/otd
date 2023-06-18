<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddThemeColorSettingsToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::table('settings')->insert([
            ['name' => 'primary_color', 'value' => '#1a2b40'],
            ['name' => 'accent_color', 'value' => '#f29181'],
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
            'primary_color',
            'accent_color',
        ])->delete();
    }
}

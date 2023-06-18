<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDashboardVirtualRoomColumnToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('settings')->insert([
            [
                'name' => 'is_dashboard_virtual_room_enabled',
                'value' => 1,
            ],
            [
                'name' => 'dashboard_virtual_room_id',
                'value' => null,
            ],
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
            'is_dashboard_virtual_room_enabled',
            'dashboard_virtual_room_id',
        ])->delete();
    }
}

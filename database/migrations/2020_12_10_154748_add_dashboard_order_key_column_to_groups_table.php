<?php

use App\Group;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDashboardOrderKeyColumnToGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->integer('dashboard_order_key')->nullable()->unsigned();
        });

        $groups = Group::all();

        foreach($groups as $group)
        {
            $group->update(['dashboard_order_key' => $group->order_key]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('dashboard_order_key');
        });
    }
}

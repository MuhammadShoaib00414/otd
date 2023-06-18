<?php

use App\Invitation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddToGroupsColumnInInvitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->json('add_to_groups')->nullable();
        });
        $invites = Invitation::all();
        foreach ($invites as $invite) {
            if ($invite->group_id) {
                $invite->add_to_groups = [$invite->group_id];
                $invite->save();
            }
        }
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropColumn('group_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropColumn('add_to_groups');
            $table->integer('group_id')->nullable();
        });
    }
}

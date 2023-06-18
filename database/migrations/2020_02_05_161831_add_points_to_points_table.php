<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Point;

class AddPointsToPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $toInsert = [[
                'key' => 'create-ideation',
                'name' => 'Create Ideation',
                'value' => 5,
                'description' => 'Awarded when an a user creates an ideation.',
            ],
            [
                'key' => 'view-ideation',
                'name' => 'View Ideation',
                'value' => 1,
                'description' => 'Awarded when an a user views an ideation.',
            ],
            [
                'key' => 'ideation-reply',
                'name' => 'Reply to Ideation',
                'value' => 1,
                'description' => 'Awarded when an a user replies to an ideation.',
            ],
            [
                'key' => 'ideation-invite',
                'name' => 'Invite to Ideation',
                'value' => 1,
                'description' => 'Awarded when an a user invites another user to an ideation.',
            ],
            [
                'key' => 'create-discussion',
                'name' => 'Create a Discussion',
                'value' => 1,
                'description' => 'Awarded when an a user creates a discussion.',
            ],
            [
                'key' => 'discussion-reply',
                'name' => 'Reply to a Discussion',
                'value' => 1,
                'description' => 'Awarded when an a user replies to a discussion.',
            ],
            [
                'key' => 'add-expense',
                'name' => 'Add an expense',
                'value' => 1,
                'description' => 'Awarded when an a user adds an expense to a budget.',
            ],
            [
                'key' => 'view-budget',
                'name' => 'View a Budget',
                'value' => 1,
                'description' => 'Awarded when an a user views a budget.',
            ]];
        foreach($toInsert as $insert) {
            DB::table('points')->insert($insert);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        $keys = ['view-budget', 'add-expense', 'discussion-reply',
                 'create-discussion', 'ideation-invite', 'ideation-reply', 
                 'view-ideation', 'create-ideation'];

        foreach($keys as $key) {
            Point::where('key', '=', $key)->delete();
        }

        Schema::enableForeignKeyConstraints();
    }
}

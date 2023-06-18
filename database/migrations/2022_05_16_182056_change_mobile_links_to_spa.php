<?php

use App\MobileLink;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMobileLinksToSpa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $home_link = MobileLink::where('url', '/home')->first();

        $home_link->update([
            'url' => '/spa#/',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $home_link = MobileLink::where('url', '/spa#')->first();

        $home_link->update([
            'url' => '/home',
        ]);
    }
}

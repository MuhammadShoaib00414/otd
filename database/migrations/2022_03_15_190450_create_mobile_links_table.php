<?php

use App\MobileLink;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMobileLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobile_links', function (Blueprint $table) {
            $table->id();
            $table->string('icon_url');
            $table->string('url');
            $table->json('defaults');
            $table->boolean('is_editable')->default(1);
            $table->timestamps();
        });

        $home_defaults = [
            'icon_url' => '/icons/home.png',
            'url' => '/home',
            'is_editable' => 0,
        ];
        $home_defaults['defaults'] = $home_defaults;
        MobileLink::create($home_defaults);

        $messages_defaults = [
            'icon_url' => '/icons/messages.svg',
            'url' => '/messages',
            'is_editable' => 1,
        ];
        $messages_defaults['defaults'] = $messages_defaults;
        MobileLink::create($messages_defaults);

        $calendar_defaults = [
            'icon_url' => '/icons/calendar.png',
            'url' => '/calendar',
            'is_editable' => 1,
        ];
        $calendar_defaults['defaults'] = $calendar_defaults;
        MobileLink::create($calendar_defaults);

        $browse_defaults = [
            'icon_url' => '/icons/magnifyingglass.png',
            'url' => '/browse',
            'is_editable' => 1,
        ];
        $browse_defaults['defaults'] = $browse_defaults;
        MobileLink::create($browse_defaults);

        $profile_defaults = [
            'icon_url' => '/icons/user.png',
            'url' => '/my-profile',
            'is_editable' => 1,
        ];
        $profile_defaults['defaults'] = $profile_defaults;
        MobileLink::create($profile_defaults);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mobile_links');
    }
}

<?php

use App\Badge;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixBadgeIconUrls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $badges = Badge::all();

        $defaults = collect([
            'images/icons8-share.svg',
            'images/icons8-share.svg',
            'images/icons8-briefcase.svg',
            'images/icons8-speech-bubble.svg',
            'images/icons8-mentor.png'
        ]);

        foreach($badges as $badge)
        {
            if($defaults->contains($badge->getRawOriginal('icon')))
            {
                $badge->update([
                    'icon' => null,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $defaults = collect([
            1 =>'images/icons8-share.svg',
            2 => 'images/icons8-share.svg',
            3 => 'images/icons8-briefcase.svg',
            4 => 'images/icons8-speech-bubble.svg',
            5 => 'images/icons8-mentor.png'
        ]);
        foreach(Badge::whereNull('icon')->where('id', '<=', 5)->get() as $badge)
        {
            $badge->update([
                'icon' => $defaults[$badge->id],
            ]);
        }
    }
}

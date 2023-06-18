<?php

namespace App\Http\Controllers\Group;

use App\Group;
use App\Http\Controllers\Controller;
use App\Lounge;
use App\VideoRoom;
use Illuminate\Http\Request;

class LoungeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'group']);
    }

    public function show(Group $group, Request $request)
    {
        if (!$group->lounge)
            return redirect('/groups/'.$group->slug);

        $lounge = $group->lounge;

        if (!$group->lounge->videoRoom) {
            $this->makeVideoRoomForLounge($group->lounge);
            $lounge = $group->lounge->fresh();
        }

        $request->user()->logs()->create([
            'action' => 'viewed lounge',
            'related_model_type' => 'App\Group',
            'related_model_id' => $group->id,
        ]);

        $agent = new \Jenssegers\Agent\Agent;

        if(($agent->isDesktop() || !$lounge->mobile_virtual_room_id || !$lounge->mobile_virtual_room->image_path) && $lounge->virtual_room && $lounge->virtual_room->image_path)
            $room = $lounge->virtual_room;
        elseif($agent->isMobile() && $lounge->mobile_virtual_room_id && $lounge->mobile_virtual_room->image_path)
            $room = $lounge->mobile_virtual_room;
        else
            $room = false;

        return view('groups.lounge.show')->with([
            'group' => $group,
            'lounge' => $lounge,
            'room' => $room,
        ]);
    }

    public function makeVideoRoomForLounge(Lounge $lounge)
    {
        $room = VideoRoom::updateOrCreate([
            'attachable_type' => 'App\Lounge',
            'attachable_id' => $lounge->id,
          ], 
          [
            'is_enabled' => 1,
            'auto_join' => 1,
          ]
        );
    }
}

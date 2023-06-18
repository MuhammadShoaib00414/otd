<?php

namespace App\Http\Controllers\Admin;

use App\Group;
use App\ClickArea;
use App\VirtualRoom;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoungeController extends Controller
{
    public function roomEditor($id, Request $request)
    {
        $group = Group::find($id);
        $room = $request->has('isMobile') ? $group->lounge->mobile_virtual_room : $group->lounge->virtual_room;
        if ($room)
            $areas = $room->clickAreas;
        else
        {
            $areas = collect([]);
            if($request->has('isMobile'))
            {
                $room = VirtualRoom::create([
                    'group_id' => $id,
                    'is_mobile' => true,
                ]);

                $group->lounge->update(['mobile_virtual_room_id' => $room->id]);
            }
        }
        
        return view('admin.groups.lounge')->with([
            'group' => $group,
            'areas' => $areas->toJson(),
            'room' => $room,
        ]);
    }

    public function saveSettings($id, Request $request)
    {
        $group = Group::find($id);

        $group->update(['enable_video_conference_in_lounge' => $request->enable_video_conference_in_lounge]);

        if($request->has('zoom_meeting_link'))
        {
            $data = $this->parseZoomLink($request->zoom_meeting_link);
            if(!$data)
                return redirect('/admin/groups/'.$id.'/lounge');
            $group->lounge->update([
                'zoom_invite_link' => $data['zoom_invite_link'],
                'zoom_meeting_id' => $data['zoom_meeting_id'],
                'zoom_meeting_password' => $data['zoom_meeting_password'],
            ]);
        }

        return redirect('/admin/groups/'.$id.'/lounge');
    }

    public function parseZoomLink($link)
    {
        if(!array_key_exists(4, explode('/', $link)))
            return false;

        $exploded_link = explode('/', $link);
        $relevant_information = $exploded_link[array_key_last($exploded_link)];

        $data = explode('?pwd=', $relevant_information);

        return [
            'zoom_meeting_id' => $data[0],
            'zoom_meeting_password' => array_key_exists(1, $data) ? $data[1] : '',
            'zoom_invite_link' => $link,
        ];
    }

    public function saveAreas($id, Request $request)
    {
        $group = Group::find($id);
        $room = $request->has('is_mobile') ? $group->lounge->mobile_virtual_room : $group->lounge->virtual_room;
        $room->clickAreas()->delete();
        $virtualRoomId = $room->id;

        $areas = collect($request->click_areas);
        $areas = $areas->map(function ($area) use ($virtualRoomId) {
            ClickArea::create([
                'width' => $area['width'],
                'height' => $area['height'],
                'x_coor' => $area['left'],
                'y_coor' => $area['top'],
                'virtual_room_id' => $virtualRoomId,
                'target_url' => $area['url'],
                'a_target' => $area['target'],
            ]);
        });

        return 'success';
    }

    public function changeImage($id, Request $request)
    {
        $request->validate([
            'photo' => 'file|max:51200',
        ]);
        $group = Group::find($id);
        $room = $request->has('is_mobile') ? $group->lounge->mobile_virtual_room : $group->lounge->virtual_room;
        $room->update([
            'image_path' => $request->file('photo')->store('virtual-rooms', 's3'),
        ]);

        return redirect('/admin/groups/'.$id.'/lounge'.($request->has('is_mobile') ? '?isMobile=true' : ''));
    }
}

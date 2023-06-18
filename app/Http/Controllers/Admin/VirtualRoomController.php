<?php

namespace App\Http\Controllers\Admin;

use App\Group;
use App\Setting;
use App\ClickArea;
use App\VirtualRoom;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class VirtualRoomController extends Controller
{
    public function roomEditor($id, Request $request)
    {
        $group = Group::find($id);
        if($request->has('isMobile') && $request->isMobile == true)
            $room = $group->mobile_virtual_room;
        else
            $room = $group->virtual_room;

        if ($room)
            $areas = $room->clickAreas;
        else
            $areas = collect([]);

        return view('admin.groups.virtualroom')->with([
            'group' => $group,
            'room' => $room,
            'areas' => $areas->toJson(),
        ]);
    }

    public function newRoom($id, Request $request)
    {
        $request->validate([
            'photo' => 'file|max:51200',
        ]);

        VirtualRoom::create([
            'group_id' => $id,
            'image_path' => $request->file('photo')->store('virtual-rooms', 's3'),
            'is_mobile' => $request->has('is_mobile'),
        ]);

        return redirect('/admin/groups/'.$id.'/virtual-room'. ($request->has('is_mobile') ? '?isMobile=true' : ''));
    }

    public function saveAreas($id, Request $request)
    {
        $group = Group::find($id);
        $room = $request->has('is_mobile') ? $group->mobile_virtual_room : $group->virtual_room;

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
        Cache::forget('dashboard-header-for-api');
        return 'success';
    }

    public function changeImage($id, Request $request)
    {
        $request->validate([
            'photo' => 'file|max:51200',
        ]);
        $group = Group::find($id);

        $room = $request->has('is_mobile') ? $group->mobile_virtual_room : $group->virtual_room;

        $room->update([
            'image_path' => $request->file('photo')->store('virtual-rooms', 's3'),
        ]);

        return redirect('/admin/groups/'.$id.'/virtual-room'.($request->has('is_mobile') ? '?isMobile=true' : ''));
    }

    public function edit($room) {
        if(!$room)
        {
            $room = VirtualRoom::create(['is_mobile' => 1]);
            Setting::where('name', 'mobile_dashboard_virtual_room_id')->update(['value' => $room->id]);
            Cache::forget('settings');
        }
        else
            $room = VirtualRoom::find($room);

        return view('admin.virtualrooms.edit')->with([
            'virtualRoom' => $room,
            'areas' => $room->clickAreas->toJson(),
        ]);
    }

    public function saveAreasByRoom(VirtualRoom $room, Request $request)
    {
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
        Cache::forget('dashboard-header-for-api');
        return 'success';
    }

     public function changeImageByRoom(VirtualRoom $room, Request $request) {
        $request->validate([
            'photo' => 'file|max:51200',
        ]);
        $room->update([
            'image_path' => $request->file('photo')->store('virtual-rooms', 's3'),
        ]);

        return redirect('/admin/virtual-rooms/'.$room->id.'/edit');
    }
}

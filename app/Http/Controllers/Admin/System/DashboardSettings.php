<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardSettings extends Controller
{
    public function edit()
    {
        return view('admin.system.dashboard-settings')->with([
            'dashboard_header_image' => Setting::where('name', 'dashboard_header_image')->first(),
            'dashboard_left_nav_image' => Setting::where('name', 'dashboard_left_nav_image')->first(),
            'dashboard_left_nav_image_link' => Setting::where('name', 'dashboard_left_nav_image_link')->first(),
            'does_dashboard_left_nav_image_open_new_tab' => Setting::where('name', 'does_dashboard_left_nav_image_open_new_tab')->first(),
            'my_groups_page_name' => Setting::where('name', 'my_groups_page_name')->first(),
        ]);
    }

    public function store(Request $request)
    {
        Setting::where('name', 'does_dashboard_left_nav_image_open_new_tab')->update([
            'value' => $request->has('does_dashboard_left_nav_image_open_new_tab') ? 1 : 0,
        ]);
        Setting::where('name', 'dashboard_left_nav_image_link')->update([
            'value' => $request->input('dashboard_left_nav_image_link'),
        ]);
        Setting::where('name', 'my_groups_page_name')->update([
            'value' => $request->input('my_groups_page_name'),
            'localization' => $request->has('localized_my_groups_page_name') ? $request->localized_my_groups_page_name : null,
        ]);

        if ($request->dashboard_header_type == 'virtual_room') {
            Setting::updateOrCreate(['name' => 'is_dashboard_virtual_room_enabled'], ['value' => 1]);
            $dashboardVirtualRoomId = getSetting('dashboard_virtual_room_id');
            if (!$dashboardVirtualRoomId) {
                $dashboardVirtualRoom = VirtualRoom::create([]);
                Setting::updateOrCreate(['name' => 'dashboard_virtual_room_id'], ['value' => $dashboardVirtualRoom->id]);
            }
            $mobileVirtualRoomId = getSetting('mobile_dashboard_virtual_room_id');
            if (!$mobileVirtualRoomId) {
                $mobileDashboardVirtualRoom = VirtualRoom::create(['is_mobile'=> 1]);
                Setting::updateOrCreate(['name' => 'mobile_dashboard_virtual_room_id'], ['value' => $mobileDashboardVirtualRoom->id]);
            }
        } elseif ($request->dashboard_header_type == 'image')
            Setting::updateOrCreate(['name' => 'is_dashboard_virtual_room_enabled'], ['value' => 0]);

        if($request->has('dashboard_header_image') && !$request->has('dashboard_header_image_revert'))
            Setting::updateOrCreate(['name' => 'dashboard_header_image'], ['value' => $request->dashboard_header_image->store('dashboard-header', 's3')]);
        else if($request->has('dashboard_header_image_revert'))
            Setting::where('name', 'dashboard_header_image')->update(['value' => null]);

        if($request->has('dashboard_left_nav_image_remove'))
            Setting::where('name', 'dashboard_left_nav_image')->update(['value' => null]);
        else if($request->has('dashboard_left_nav_image'))
            Setting::updateOrCreate(['name' => 'dashboard_left_nav_image'], ['value' => $request->dashboard_left_nav_image->store('images', 's3')]);
        if($request->has('dashboard_left_nav_image_localization') || $request->has('dashboard_left_nav_image_es_remove'))
        {
            $left_nav_localization['es']['dashboard_left_nav_image'] = $request->has('dashboard_left_nav_image_remove_es') ? null : $request->dashboard_left_nav_image_localization['es']['dashboard_left_nav_image']->store('images', 's3');
            Setting::where('name', 'dashboard_left_nav_image')->update(['localization' => $left_nav_localization]);
        }

        Cache::forget('settings');
        Cache::forget('settings-for-api');
        Cache::forget('dashboard-header-for-api');

        return redirect()->back()->with('success', 'Settings saved!');
    }
}

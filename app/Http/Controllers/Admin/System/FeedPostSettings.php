<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FeedPostSettings extends Controller
{
    public function edit()
    {
        return view('admin.system.feed-post-settings')->with([
            'is_ideations_enabled' => Setting::where('name', 'is_ideations_enabled')->first()->value,
            'is_ideation_approval_enabled' => Setting::where('name', 'is_ideation_approval_enabled')->first()->value,
            'is_likes_enabled' => Setting::where('name', 'is_likes_enabled')->first()->value,
        ]);
    }

    public function store(Request $request)
    {
        Setting::where('name', 'is_likes_enabled')->update([
            'value' => $request->is_likes_enabled,
        ]);
        Setting::where('name', 'is_ideations_enabled')->update([
            'value' => $request->is_ideations_enabled,
        ]);
        Setting::where('name', 'is_ideation_approval_enabled')->update([
            'value' => $request->is_ideations_enabled,
        ]);

        Cache::forget('settings');
        Cache::forget('settings-for-api');
        Cache::forget('dashboard-header-for-api');

        return redirect()->back()->with('success', 'Settings saved!');
    }

}

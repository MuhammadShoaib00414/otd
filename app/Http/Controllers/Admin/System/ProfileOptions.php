<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProfileOptions extends Controller
{
    public function edit()
    {
        return view('admin.system.profile-options')->with([
            'is_superpower_enabled' => Setting::where('name', 'is_superpower_enabled')->first()->value,
            'is_about_me_enabled' => Setting::where('name', 'is_about_me_enabled')->first()->value,
            'is_job_title_enabled' => Setting::where('name', 'is_job_title_enabled')->first()->value,
            'is_company_enabled' => Setting::where('name', 'is_company_enabled')->first()->value,
        ]);
    }

    public function store(Request $request)
    {
        Setting::where('name', 'is_superpower_enabled')->update(['value' => $request->is_superpower_enabled]);
        Setting::where('name', 'is_about_me_enabled')->update(['value' => $request->is_about_me_enabled]);
        Setting::where('name', 'is_company_enabled')->update(['value' => $request->is_company_enabled]);
        Setting::where('name', 'is_job_title_enabled')->update(['value' => $request->is_job_title_enabled]);

        Cache::forget('settings');
        Cache::forget('settings-for-api');

        return redirect()->back()->with('success', 'Saved!');
    }
}

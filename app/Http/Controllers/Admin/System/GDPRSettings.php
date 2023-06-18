<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GDPRSettings extends Controller
{
    public function edit()
    {
        return view('admin.system.gdpr-settings')->with([
            
        ]);
    }

    public function store(Request $request)
    {
        Setting::where('name', 'gdpr_prompt')->update(['value' => $request->gdpr_prompt]);
        Setting::where('name', 'gdpr_checkbox_label')->update(['value' => $request->gdpr_checkbox_label]);

        Cache::forget('settings');

        return redirect()->back()->with('success', 'Settings saved!');
    }
}

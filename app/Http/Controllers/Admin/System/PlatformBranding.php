<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PlatformBranding extends Controller
{
    public function edit()
    {
        return view('admin.system.platform-branding')->with([
            'orgName' => Setting::where('name', '=', 'name')->first(),
            'from_email_name' => Setting::where('name', 'from_email_name')->first(),
            'logo' => Setting::where('name', 'logo')->first(),
            'primary_color' => Setting::where('name', 'primary_color')->first(),
            'accent_color' => Setting::where('name', 'accent_color')->first(),
            'navbar_color' => Setting::where('name', 'navbar_color')->first(),
            'group_header_color' => Setting::where('name', 'group_header_color')->first()->value,
        ]);
    }

    public function store(Request $request)
    {
        Setting::where('name', '=', 'name')->update([
            'value' => $request->name,
            'localization' => $request->localized_name,
        ]);
        Setting::where('name', '=', 'from_email_name')->update([
            'value' => $request->from_email_name,
            'localization' => $request->localized_from_email_name,
        ]);
        Setting::where('name', '=', 'primary_color')->update([
            'value' => $request->primary_color,
        ]);
        Setting::where('name', '=', 'accent_color')->update([
            'value' => $request->accent_color,
        ]);
        Setting::where('name', '=', 'navbar_color')->update([
            'value' => $request->navbar_color,
        ]);
        Setting::where('name', 'group_header_color')->update([
            'value' => $request->group_header_color,
        ]);

        if($request->has('logo_revert'))
            Setting::where('name', 'logo')->update(['value' => '/images/logo-2.png']);
        else if($request->has('logo'))
            Setting::where('name', 'logo')->update(['value' => $request->logo->store('images', 's3')]);

        if($request->has('logo_localization') || $request->has('logo_revert_es')) {
            $localization['es']['logo'] = $request->has('logo_revert_es') ? '/images/logo-2.png' : $request->logo_localization['es']['logo']->store('images');
            Setting::where('name', 'logo')->update(['localization' => $localization]);
        }
        $this->makeStyles($request->primary_color, $request->accent_color, $request->navbar_color);

        Cache::forget('settings');
        Cache::forget('theme-colors');

        return redirect()->back()->with('success', 'Settings saved!');
    }

    protected function makeStyles($primary, $accent, $navbar)
    {
        setThemeColors();
    }
}

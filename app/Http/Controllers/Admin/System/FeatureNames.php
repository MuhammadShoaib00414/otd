<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FeatureNames extends Controller
{
    public function edit()
    {

        $pages = Setting::where('name', 'pages')->get()->count();
        $page = '';
        if ($pages > 0) {
            $page = Setting::where('name', 'pages')->first()->value;
        } else {
            $page = 'Pages';
        }
        return view('admin.system.feature-names')->with([
            'ask_a_mentor' => Setting::where('name', 'is_ask_a_mentor_enabled')->first()->value,
            'ask_a_mentor_alias' => Setting::where('name', 'ask_a_mentor_alias')->first(),
            'find_your_people_alias' => Setting::where('name', 'find_your_people_alias')->first(),
            'pages' => $page,
        ]);
    }

    public function store(Request $request)
    {
        $checkvalue = Setting::where('name', '=', 'pages')->count();
        if ($checkvalue > 0) {
            Setting::where('name', '=', 'pages')->update([
                'name' => 'pages',
                'value' => $request->pages,
            ]);
        } else {
            $pageInsert = new Setting;
            $pageInsert->name = 'pages';
            $pageInsert->value = $request->pages;
            $pageInsert->save();

        }
        Setting::where('name', 'is_ask_a_mentor_enabled')->update(['value' => $request->input('ask_a_mentor')]);
        Setting::where('name', 'find_your_people_alias')->update([
            'value' => $request->find_your_people_alias,
        ]);
        Setting::where('name', 'ask_a_mentor_alias')->update([
            'value' => $request->ask_a_mentor_alias,
        ]);

        Cache::forget('settings');
        Cache::forget('settings-for-api');

        return redirect()->back()->with('success', 'Settings saved!');
    }
}

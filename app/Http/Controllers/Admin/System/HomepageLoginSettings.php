<?php

namespace App\Http\Controllers\Admin\System;

use App\HomePageImage;
use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomepageLoginSettings extends Controller
{
    public function edit()
    {
        return view('admin.system.homepage-login')->with([
            'account_created_message' => Setting::where('name', 'account_created_message')->first(),
            'open_registration' => Setting::where('name', 'open_registration')->first()->value,
            'home_page_images' => HomePageImage::where('lang', 'en')->get(),
            'home_page_images_es' => HomePageImage::where('lang', 'es')->get(),
            'hide_new_members' => Setting::where('name', 'hide_new_members')->first()->value,
            'group_admins' => Setting::where('name', 'group_admins')->first()->value,
            'homepage_text' => Setting::where('name', 'homepage_text')->first(),
        ]);
    }

    public function store(Request $request)
    {
        Setting::updateOrCreate(['name' => 'account_created_message'], ['value' => $request->account_created_message, 
            'localization' => $request->has('localization') ? ['es' => ['account_created_message' => $request->localization['es']['account_created_message']]] : '',
        ]);
        Setting::updateOrCreate(['name' => 'open_registration'], ['value' => $request->open_registration]);
        Setting::updateOrCreate(['name' => 'hide_new_members'], ['value' => $request->hide_new_members]);
        Setting::updateOrCreate(['name' => 'group_admins'], ['value' => $request->group_admins]);
        Setting::updateOrCreate(['name' => 'homepage_text'], [
            'value' => $request->homepage_text,
            'localization' => $request->has('localization') ? ['es' => ['homepage_text' => $request->localization['es']['homepage_text']]] : '',
        ]);
        if($request->open_registration && !Setting::where('name', 'registration_key')->count()) {
            Setting::create([
                'name' => 'registration_key',
                'value' => substr(Hash::make(env('APP_KEY')), 0, 6),
            ]);
        }
        if($request->has('home_page_images'))
        {
            foreach($request->home_page_images as $id => $image)
            {
                HomePageImage::where('id', $id)->update([
                    'image_url' => $image->store('images', 's3')
                ]);
            }
        }
        if($request->has('home_page_image_remove'))
        {
            $toRemove = collect($request->home_page_image_remove)->filter(function($val) {
                return $val == true;
            });
            HomePageImage::whereIn('id', $toRemove->keys())->update(['image_url' => null]);
        }

        if($request->has('home_page_image_localization') || $request->has('home_page_image_revert_es'))
        {
            $home_page_image_localization['es']['home_page_image'] = $request->has('home_page_image_revert_es') ? null : '/uploads/' . $request->home_page_image_localization['es']['home_page_image']->store('images', 's3');
            Setting::where('name', 'home_page_image')->update(['localization' => $home_page_image_localization]);
        }

        Cache::forget('settings');
        Cache::forget('settings-for-api');

        return redirect()->back()->with('success', 'Saved!');
    }
}

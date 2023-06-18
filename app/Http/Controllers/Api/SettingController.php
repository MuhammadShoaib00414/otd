<?php

namespace App\Http\Controllers\Api;

use App\Setting;
use App\MobileLink;
use App\VirtualRoom;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function getsettings(Request $request)
    {
        $settings = Cache::remember('settings-for-api', 1800, function () {
            $settings = Setting::pluck('value', 'name')->toArray();
            $settings['themes'] = getThemeColors();
            $settings['base_url'] = config('app.url');

            foreach($settings as $name => &$value) {
                if(!$value)
                    $value = false;
            }
            if ($settings['dashboard_left_nav_image'])
                $settings['dashboard_left_nav_image'] = getS3Url($settings['dashboard_left_nav_image']);

            if($tutorial = \App\Tutorial::where('name', 'Personal Dashboard')->first())
                $settings['dashboard_tutorial'] = $tutorial;

            return $settings;
        });
    
        return response()->json($settings);
    }

    public function getDashboardHeader(Request $request)
    {
        $response = Cache::remember('dashboard-header-for-api', 18000, function () {
            if(getsetting('is_dashboard_virtual_room_enabled')) {
                $desktopRoom = VirtualRoom::find(getSetting('dashboard_virtual_room_id'));
                $mobileRoom = VirtualRoom::find(getSetting('mobile_dashboard_virtual_room_id'));
                $desktopRoom->image_url = $desktopRoom->image_url;
                $desktopRoom->click_areas = $desktopRoom->clickAreas;
                $mobileRoom->image_url = $mobileRoom->image_url;
                $mobileRoom->click_areas = $mobileRoom->clickAreas;
                $header_image = false;
            } else {
                $header_image = getsetting('dashboard_header_image');
                $desktopRoom = false;
                $mobileRoom = false;
            }

            $response['desktop_virtual_room'] = $desktopRoom;
            $response['mobile_virtual_room'] = $mobileRoom;
            $response['header_image'] = $header_image;

            return $response;
        });

        return $response;
    }

    public function getMobileLinks()
    {
        return MobileLink::all();
    }   

    public function localization(Request $request)
    {
        $output = Cache::remember('localization-for-api-user-'.$request->user()->id, 18000, function () use ($request) {
            $langDirectoryPath = base_path() . "/resources/lang";
            $locales = [];
            $output = [];
            foreach (scandir($langDirectoryPath) as $folder) {
                if (!in_array($folder, [".", ".."]))
                    $locales[] = $folder;
            }
            foreach ($locales as $locale) {
                $localeDirPath = $langDirectoryPath . "/" . $locale;
                $filesInLocaleFolder = scandir($localeDirPath);
                foreach ($filesInLocaleFolder as $file) {
                    if (in_array($file, [".", ".."])) continue;

                    $fullPathToFile = $localeDirPath . "/" . $file;
                    $output[$locale][str_replace(".php", "", $file)] = include($fullPathToFile);
                }
            }

            return $output[$request->user()->locale];
        });
        
        return response()->json($output);
    }
}   

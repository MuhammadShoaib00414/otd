<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Point;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PointsController extends Controller
{
    public function index()
    {
        return view('admin.points.index')->with([
            'points' => Point::all(),
            'is_points_enabled' => Setting::where('name', 'is_points_enabled')->first()->value,
        ]);
    }
    
    public function update(Request $request)
    {
        $points = $request->input('points');

        foreach($points as $key => $value) {
            Point::where('key', '=', $key)->update(['value' => $value]);
        }

        Setting::where('name', 'is_points_enabled')->update([
            'value' => $request->input('is_points_enabled'),
        ]);

        Cache::forget('settings');
        Cache::forget('settings-for-api');
        Cache::forget('dashboard-header-for-api');

        return redirect('/admin/points')->with('success', 'Saved!');
    }
    
}

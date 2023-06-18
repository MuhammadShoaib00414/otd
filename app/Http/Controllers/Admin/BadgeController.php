<?php

namespace App\Http\Controllers\Admin;

use App\Badge;
use App\Scopes\EnabledScope;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class BadgeController extends Controller
{
    public function index()
    {
        return view('admin.badges.index')->with([
            'badges' => Badge::withoutGlobalScope(EnabledScope::class)->get(),
        ]);
    }

    public function create()
    {
        return view('admin.badges.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'icon' => 'required|file|max:51200',
        ]);

        $badgeIcon = $request->icon->store('badges', 's3');
        $badgeIcon = str_replace('//', '/', $badgeIcon);
        $badgeIcon = getS3Url($badgeIcon);
        if ($badgeIcon[0] == '/') {
            $badgeIcon = substr($badgeIcon, 1);
        }

        Badge::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'created_by_id' => $request->user()->id,
            'localization' => $request->localization,
            'icon' => $badgeIcon,
        ]);

        return redirect('/admin/badges');
    }

    public function edit($id, Request $request)
    {
        return view('admin.badges.edit')->with([
            'badge' => Badge::withoutGlobalScope(EnabledScope::class)->where('id', $id)->first(),
        ]);
    }

    public function update($id, Request $request)
    {
        $badge = Badge::withoutGlobalScope(EnabledScope::class)->where('id', '=', $id)->first();
        $badgeIcon = $badge->icon;
        if ($request->icon != null) {
            $badgeIcon = $request->icon->store('badges', 's3');
            Storage::disk('s3')->delete($badge->icon);
            $badgeIcon = str_replace('//', '/', $badgeIcon);
            $badgeIcon = getS3Url($badgeIcon);
        }
        if ($badgeIcon[0] == '/') {
            $badgeIcon = substr($badgeIcon, 1);
        }
        $badge->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'localization' => $request->localization,
            'is_enabled' => $request->has('is_enabled'),
            'icon' => $badgeIcon,
        ]);

        return redirect('/admin/badges');
    }

    public function delete($id)
    {
        $badge = Badge::withoutGlobalScope(EnabledScope::class)->where('id', $id)->first();
        Storage::disk('s3')->delete($badge->icon);
        $badge->users()->sync([]);
        $badge->delete();

        return redirect('/admin/badges');
    }

}

<?php

namespace App\Http\Controllers\Admin;

use App\File;
use App\Group;
use App\Folder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class GroupFileController extends Controller
{
    public function index($group)
    {
        $group = Group::withTrashed()->find($group);
        $items = $group->folders->sortBy('name')->concat($group->files()->whereNull('folder_id')->orderBy('name', 'asc')->get());

        return view('admin.groups.files')->with([
            'folder' => null,
            'items' => $items,
            'group' => $group,
        ]);
    }

    public function folder($group, $folder, Request $request)
    {
        $group = Group::withTrashed()->find($group);
        $folder = Folder::find($folder);

        return view('admin.groups.files')->with([
            'folder' => $folder,
            'items' => $folder->files,
            'group' => $group,
        ]);
    }

    public function downloadFile($group, $file, Request $request)
    {
        $file = File::find($file);

        $mimeType = Storage::disk('s3')->mimeType($file->getRawOriginal('path'));

        $headers = [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => 'attachment; filename="'. $file->name .'"',
        ];
 
        return \Response::make(Storage::disk('s3')->get($file->getRawOriginal('path')), 200, $headers);
    }
}

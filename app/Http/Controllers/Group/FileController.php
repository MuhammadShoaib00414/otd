<?php

namespace App\Http\Controllers\Group;

use App\File;
use App\Group;
use App\Folder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'group']);
    }
    
    public function index($slug, Request $request)
    {
        $group = Group::where('slug', '=', $slug)->first();
        $items = $group->folders->sortBy('name')->concat($group->files()->whereNull('folder_id')->orderBy('name', 'asc')->get());

        return view('groups.files.index')->with([
            'folder' => null,
            'items' => $items,
            'group' => $group,
        ]);
    }

    public function folder($slug, $folder, Request $request)
    {
        $group = Group::where('slug', '=', $slug)->first();
        $folder = Folder::find($folder);

        return view('groups.files.index')->with([
            'folder' => $folder,
            'items' => $folder->files()->orderBy('name', 'asc')->get(),
            'group' => $group,
        ]);
    }

    public function storeFolder($slug, Request $request)
    {
        $group = Group::where('slug', '=', $slug)->first();
        $folder = Folder::create([
            'name' => $request->name,
            'group_id' => $group->id,
        ]);

        return redirect('/groups/'.$slug.'/files');
    }

    public function upload($slug, Request $request)
    {
        $validation = $request->validate([
            'document' => 'required|file|max:51200',
        ]);
        
        $group = Group::where('slug', '=', $slug)->first();
        if ($request->has('document')) {
            $file = $request->file('document');
            $path = $request->file('document')->store('documents/'.$slug, 's3');

            File::create([
                'path' => $path,
                'name' => $file->getClientOriginalName(),
                'folder_id' => ($request->folder == 'null') ? null : $request->folder,
                'group_id' => $group->id,
            ]);

            if ($request->folder != 'null')
                return redirect('/groups/'.$slug.'/files/'.$request->folder);
            else
                return redirect('/groups/'.$slug.'/files');
        }
    }

    public function deleteFile($slug, $file, Request $request)
    {
        $file = File::find($file);
        $file->delete();

        if ($file->folder_id != 'null')
            return redirect('/groups/'.$slug.'/files/'.$file->folder_id);
        else
            return redirect('/groups/'.$slug.'/files/');
    }

    public function deleteFolder($slug, $folder, Request $request)
    {
        $folder = Folder::find($folder);
        if ($folder->files()->count() > 0) {
            $request->session()->flash('error', "Error: You cannot delete a folder that contains files.");

            return back();
        }

        $folder->delete();

        return redirect('/groups/'.$slug.'/files/');
    }

    public function download($slug, $file, Request $request)
    {
        $file = File::find($file);

        $request->user()->logs()->create([
            'action' => 'downloaded file',
            'related_model_type' => 'App\Group',
            'related_model_id' => Group::where('slug', $slug)->first()->id,
            'secondary_related_model_type' => 'App\File',
            'secondary_related_model_id' => $file->id,
        ]);

        $mimeType = Storage::disk('s3')->mimeType($file->getRawOriginal('path'));

        $headers = [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => 'attachment; filename="'. $file->name .'"',
        ];

        return Storage::disk('s3')->download($file->getRawOriginal('path'), $file->name, $headers);
 
        // return \Response::make(Storage::disk('s3')->get($file->getRawOriginal('path')), 200, $headers);
    }

}

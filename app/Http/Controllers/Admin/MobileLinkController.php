<?php

namespace App\Http\Controllers\Admin;

use App\MobileLink;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MobileLinkController extends Controller
{
    public function __construct()
    {
        return $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        return view('admin.mobile.index')->with([
            'links' => MobileLink::all(),
        ]);
    }

    public function update(Request $request)
    {
        $links = $request->links;
        foreach($links as $id => $link)
        {
            $mobileLink = MobileLink::find($id);
            if(isset($link['revert']))
            {
                $mobileLink->restoreDefaults();
                continue;
            }
            if(isset($link['icon']))
            {
                $file = '/icons/' . $request->file('links')[$id]['icon']->store('', 'icons');
                $mobileLink->update([
                    'icon_url' => $file,
                ]);
            }
            if(isset($link['url']))
            {
                $url = $link['url'];
                if(!str_contains($url, config('app.url')))
                    continue;
                $url = str_replace(config('app.url'), '', $url);
                $mobileLink->update([
                    'url' => $url,
                ]);
            }
        }

        return redirect('/admin/mobile');
    }
}

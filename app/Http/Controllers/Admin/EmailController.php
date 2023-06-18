<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmailController extends Controller
{
    public function overview()
    {
        return redirect('/admin/emails/campaigns');
    }

    public function uploadImage(Request $request)
    {
        $url = $request->file('file')->store('email-images', 'public_old');

        return response()->json(['url' => config('app.url') . "/uploads/" . $url ]);
    }
    
}

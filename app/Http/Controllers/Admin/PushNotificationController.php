<?php

namespace App\Http\Controllers\Admin;

use App\PushNotification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PushNotificationController extends Controller
{
    public function __construct()
    {
        return $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        return view('admin.notifications.push.index')->with([
            'notifications' => PushNotification::all(),
        ]);
    }

    public function edit($id)
    {
        return view('admin.notifications.push.edit')->with([
            'notification' => PushNotification::find($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|max:25',
            'body' => 'required|max:50',
        ]);

        PushNotification::where('id', $id)->update([
            'title' => $request->title,
            'body' => $request->body,
            'is_enabled' => $request->has('is_enabled'),
        ]); 

        return redirect('/admin/notifications/push');
    }
}

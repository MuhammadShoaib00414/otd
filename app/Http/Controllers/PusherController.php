<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Pusher\Pusher;

class PusherController extends Controller
{
    public function auth(Request $request)
    {
        $pusher = new Pusher(config('otd.pusher_key'), config('otd.pusher_secret'), config('otd.pusher_app_id'),['cluster' => config('otd.pusher_cluster')]);
        $presence_data = [
            'name' => $request->user()->name
        ];
        $pusherName = $request->user()->id;

        return $pusher->presence_auth($_POST['channel_name'], $_POST['socket_id'], $pusherName, $presence_data);
    }
}

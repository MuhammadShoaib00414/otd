<?php

namespace App\Http\Controllers;

use App\Group;
use App\Event;
use App\Http\Controllers\Controller;
use App\Traits\ZoomTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class ZoomMeetingController extends Controller
{
    use ZoomTrait;

    const MEETING_TYPE_INSTANT = 1;
    const MEETING_TYPE_SCHEDULE = 2;
    const MEETING_TYPE_RECURRING = 3;
    const MEETING_TYPE_FIXED_RECURRING_FIXED = 8;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'topic' => 'required|string',
            'start_time' => 'required|date',
            'agenda' => 'string|nullable',
        ]);
        
        if ($validator->fails()) {
            return [
                'success' => false,
                'data' => $validator->errors(),
            ];
        }
        $data = $validator->validated();

        $path = 'users/me/meetings';
        $response = $this->zoomPost($path, [
            'topic' => $data['topic'],
            'type' => self::MEETING_TYPE_SCHEDULE,
            'start_time' => $this->toZoomTimeFormat($data['start_time']),
            'duration' => 30,
            'agenda' => $data['agenda'],
            'settings' => [
                'host_video' => false,
                'participant_video' => false,
                'waiting_room' => true,
            ]
        ]);


        return [
            'success' => $response->status() === 201,
            'data' => json_decode($response->body(), true),
        ];
    }

    public function get(Request $request, string $id)
    {
        $agent = new \Jenssegers\Agent\Agent;
        if($agent->browser() == "Safari" || $agent->isMobile())
            return view('zoom.unsupported');

        $path = 'meetings/' . $id;
        $response = $this->zoomGet($path);

        // dd(json_decode($response->body(), true));

        // $data = json_decode($response->body(), true);
        // if ($response->ok()) {
        //     $data['start_at'] = $this->toUnixTimeStamp($data['start_time'], $data['timezone']);
        // }

        $callable = Event::where('zoom_meeting_id', $id)->first();
        if(!$callable)
            $callable = Group::where('zoom_meeting_id', $id)->first();

        return view('zoom.show')->with([
            'meeting_id' => $id,
            'callable' => $callable,
        ]);
    }

    public function getSignature(Request $request)
    {
        // date_default_timezone_set("UTC");

        $time = time() * 1000 - 30000;//time in milliseconds (or close enough)
        
        $data = base64_encode(config('otd.zoom_api_key') . $request->meeting_number . $time . $request->role);
        
        $hash = hash_hmac('sha256', $data, config('otd.zoom_api_secret'), true);
        
        $_sig = config('otd.zoom_api_key') . "." . $request->meeting_number . "." . $time . "." . $request->role . "." . base64_encode($hash);
        
        //return signature, url safe base64 encoded
        return response()->json(json_encode(rtrim(strtr(base64_encode($_sig), '+/', '-_'), '=')));
    }

    public function createUser()
    {
        $response = $this->zoomGet('/users/davis@ipx.org');
        dd($response->json());
    }

    public function showClosing()
    {
        return view('zoom.closing');
    }
}
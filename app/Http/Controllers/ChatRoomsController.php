<?php

namespace App\Http\Controllers;

use App\ChatMessage;
use App\ChatRoom;
use Illuminate\Http\Request;

class ChatRoomsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function newMessage($id, Request $request)
    {
        ChatMessage::create([
            'user_id' => $request->user()->id,
            'chat_room_id' => $id,
            'message' => $request->message,
        ]);
    }

    public function loadMessages($id, Request $request)
    {
        $room = ChatRoom::find($id);
        $messages = $room->messages()->with('user')->limit(25)->orderBy('id', 'desc')->get()->reverse();
        $messages = $messages->toArray();
        $response = [];
        foreach($messages as $message) {
            $response[] = (Object) [
                'user' => (Object) [
                    'id' => $message['user']['id'],
                    'name' => $message['user']['name'],
                ],
                'message' => $message['message'],
            ];
        }

        return response()->json(
            $response
        );
    }

}

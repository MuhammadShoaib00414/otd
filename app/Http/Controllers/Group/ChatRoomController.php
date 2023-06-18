<?php

namespace App\Http\Controllers\Group;

use App\Log;
use App\Group;
use App\Http\Controllers\Controller;
use Embed\Http\Response;
use Illuminate\Http\Request;

class ChatRoomController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'group']);
    }

    public function download($slug)
    {
        $group = Group::where('slug', $slug)->first();

        return $this->getDownload($group->chatRoom->messages);
    }

    public function clear($slug, Request $request)
    {
        $group = Group::where('slug', $slug)->first();
        optional($group->chatRoom)->messages()->delete();

        Log::create([
          'user_id' => $request->user()->id,
          'action' => 'cleared chat',
          'related_model_type' => Group::class,
          'related_model_id' => $group->id,
        ]);

        return redirect('/groups/'.$slug.'/edit')->with('success', 'Chat history cleared.');
    }

    protected function getDownload($messages) {
    // prepare content
    $content = "Chat Room \n\n";
    foreach ($messages as $message) {
      $content .= $message->user->name . ' @ ' . $message->created_at->toDateTimeString();
      $content .= "\n";
      $content .= $message->message;
      $content .= "\n\n";
    }

    // file name that will be used in the download
    $fileName = "chat-room.txt";

    // use headers in order to generate the download
    $headers = [
      'Content-type' => 'text/plain', 
      'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName),
      'Content-Length' => strlen($content),
    ];

    // make a response, with the content, a 200 response code and the headers
    return response($content)->withHeaders($headers);
}
}

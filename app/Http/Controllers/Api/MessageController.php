<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\MessageThread;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function checkForNewMessages($thread, $time, Request $request)
    {
    	$thread = MessageThread::find($thread);

    	$hasNewMessage = $thread->messages()->where('created_at', '>', $time)->exists();

    	return response()->json($hasNewMessage);
    }
}

<?php

namespace App\Http\Controllers\Api;

use Mail;
use App\User;
use Carbon\Carbon;
use App\Invitation;
use App\Mail\InviteUser;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InviteController extends Controller
{
    public function sendInvite(Request $request)
    {
        if ( ! $request->has('key') && $request->key != 'darkwoodguitar')
            return 'Invalid key';

        if ( ! $request->has('email'))
            return 200;

        if (User::where('email', '=', $request->email)->count())
            return 200;

        if (Invitation::where('email', '=', $request->email)->count()) {
            $invitation = Invitation::where('email', '=', $request->email)->first();
        } else {
            $invitation = Invitation::create([
                'email' => $request->email,
                'custom_message' => '',
                'sent_at' => Carbon::now(),
                'hash' => Str::random(7),
            ]);
        }

        $invitation->send();

        return 200;
    }
}

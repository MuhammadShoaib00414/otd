<?php

namespace App\Http\Controllers;

use App\User;
use App\Badge;
use App\Introduction;
use Illuminate\Http\Request;
use App\Events\IntroductionMade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class IntroductionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'onboarding']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return redirect('/introductions/received');
    }

    public function received(Request $request)
    {
        $introductions = $request->user()->introductions()->get();

        if(isset($_GET['s']) && $_GET['s'] != '')
        {
            $introductions = $introductions->filter(function($introduction) {
                return $introduction->users()->where('name', 'like', '%'.$_GET['s'].'%')->count();
            });
        }

        return view('introductions.received')->with([
            'introductions' => Introduction::whereIn('id', $introductions->pluck('id'))->orderBy('id', 'desc')->simplePaginate(10),
        ]);
    }

    public function sent(Request $request)
    {
        $introductions = Introduction::where('sent_by', '=', $request->user()->id)->get();

        if(isset($_GET['s']) && $_GET['s'] != '')
        {
            $introductions = $introductions->filter(function($introduction) {
                return $introduction->users()->where('name', 'like', '%'.$_GET['s'].'%')->count();
            });
        }

        return view('introductions.sent')->with([
            'introductions' => Introduction::whereIn('id', $introductions->pluck('id'))->orderBy('id', 'desc')->simplePaginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(request $request)
    {
        $groupArray = $request->user()->groups->pluck('id');
        $recipients = User::leftJoin('group_user', 'group_user.user_id', '=', 'users.id')
                        ->where('users.id', '!=', $request->user()->id)
                        ->where([
                            ['is_enabled',      '=', '1'],
                            ['is_hidden',       '=', '0'],
                        ])->whereIn('group_user.group_id', $groupArray)
                        ->groupBy('users.id')
                        ->orderBy('name', 'asc')
                        ->select(['name', 'id']);

        if($request->has('user'))
            $recipients = $recipients->where('users.id', '!=', $request->input('user'));

        $recipients = $recipients->get();

        $recipients = $recipients->map(function ($user) {
            return (object) [
                'label' => $user->name,
                'value' => $user->id,
            ];
        });
        $recipient = User::find($request->input('user'));
        return view('introductions.create')->with([
            'recipient' => $recipient,
            'recipients' => $recipients->toJson(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user1 = User::find($request->input('users')[0]);
        $user2 = User::find($request->input('users')[1]);

        if($user1->id == $user2->id)
        {
            throw ValidationException::withMessages(['user1' => 'This user has already been introduced to themself.']);
        }

        $validator = Validator::make($request->all(), []);
        
        //get the introduction IDs that user1 is in
        //see if there's a user2 value with that introduction id
       
        $user1_introduction_ids = DB::table('introduction_user')
                                ->where('user_id', $user1->id)
                                ->pluck('introduction_id');
        $user2_introductions = DB::table('introduction_user')
                                ->where('user_id', $user2->id)
                                ->whereIn('introduction_id', $user1_introduction_ids)
                                ->get();
        $user_has_introduced = false;
        //foreach introductions with these 2 users, have any been sent by the current user?
        //if so, throw an error
        foreach($user2_introductions as $introduction_raw)
        {
            $introduction = Introduction::find($introduction_raw->introduction_id);
            if($introduction && $introduction->sent_by == $request->user()->id)
            {
                $user_has_introduced = true;
            }
        }

        if($user_has_introduced)
        {
            throw ValidationException::withMessages(['user1' => 'These users have already been introduced!']);
        }

        //default value with both user IDs
        $messages_sent = json_encode([$user1->id => 0, $user2->id => 0]);

        // Create a new introduction
        $introduction = Introduction::create([
            'sent_by' => $request->user()->id,
            'message' => $request->message,
            'are_messages_sent' => $messages_sent,
        ]);
        if(Badge::count() > 0 && Badge::where('id', 1)->first())
            $request->user()->badges()->syncWithoutDetaching(1);

        if(Introduction::where('sent_by', '=', $request->user()->id)->count() >= 5)
        {
            if(Badge::count() > 0 && Badge::where('id', 2)->first())
                $request->user()->badges()->syncWithoutDetaching(2);
        }

        // Attach the users[] to the introduction to create the introduction
        $introduction->users()->attach($request->input('users'));
        event(new IntroductionMade($request->user(), $introduction));
        \Session::flash('message', "<b>Great work!</b> Your introduction has been sent!");

        return redirect('/introductions/sent');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $introduction = Introduction::find($id);

        \DB::table('introduction_user')
           ->where('user_id', '=', $request->user()->id)
           ->where('introduction_id', '=', $id)
           ->update(['is_unread' => '0']);

        event(new \App\Events\IntroductionViewed($request->user(), $introduction));

        return view('introductions.show')->with([
            'introduction' => $introduction,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $introduction = Introduction::find($id);

        if($request->user()->id != $introduction->sent_by)
        {
            return redirect('/introductions');
        }

        return view('introductions.edit')->with([
            'introduction' => $introduction,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Introduction::where('id', $id)->update(['message' => $request->input('message')]);

        return redirect('/introductions/' . $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Introduction::find($id)->delete();

        return redirect('/introductions/sent');
    }
}

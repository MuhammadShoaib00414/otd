<?php

namespace App\Http\Controllers\Api;

use DB;
use App\User;
use App\Skill;
use App\Keyword;
use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MentorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function results(Request $request)
    {
        $user_id = $request->user()->id;
        $userIds = [];
        $options = [];

        if ($request->has('options')) {
            $options = collect($request->options)->pluck('id')->all();
            $userIds = collect(DB::select(DB::raw("SELECT option_user.user_id FROM option_user where option_id in (". join(',',$options). ") and user_id != " . $user_id . " group by user_id having count(distinct option_id) = " . count($options))))->pluck('user_id');
        }

        if ($request->has('options'))
            $results = User::whereIn('id', $userIds)->visible()->where('is_mentor', '=', 1)->where('id', '!=', $user_id)->with(['options'])->get();
        else
            $results = User::with(['options'])->visible()->where('is_mentor', '=', 1)->where('id', '!=', $user_id)->get();

        return response()->json($results);
    }

}

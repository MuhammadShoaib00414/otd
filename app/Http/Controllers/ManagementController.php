<?php

namespace App\Http\Controllers;

use DB;
use App\User;
use Illuminate\Http\Request;

class ManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function directReports(Request $request)
    {
        $id = $request->user()->id;
      $users = User::whereIn('id', [$id + 1, $id + 35, $id + 24, $id + 16, $id + 19, $id + 22, $id + 3, $id + 2])->get();
      $messagesSent = $users->map(function ($user) {
        return (Object) [
          'id'    => $user->id,
          'name' => $user->name,
          'count' => round(($user->id + 150)/6),
        ]; });
      $introductionsMade = $users->map(function ($user) {
        return (Object) [
          'id'    => $user->id,
          'name' => $user->name,
          'count' => round(($user->id*20 + 150)/135),
        ]; });
      $introductionsRespondedTo = $users->map(function ($user) {
        return (Object) [
          'id'    => $user->id,
          'name' => $user->name,
          'count' => round(($user->id*20 + 150)/100),
        ]; });
      $shoutoutsMade = $users->map(function ($user) {
        return (Object) [
          'id'    => $user->id,
          'name' => $user->name,
          'count' => round(($user->id*20 + 150)/130),
        ]; });
      $shoutoutsReceived = $users->map(function ($user) {
        return (Object) [
          'id'    => $user->id,
          'name' => $user->name,
          'count' => round(($user->id%7*20 + 150)/85),
        ]; });
      $mentorBreakdown = collect([
        (Object) ['count' => $users->count() - round($users->count()*.6)],
        (Object) ['count' => round($users->count()*.6)]
      ]);
      $skillsetsPerPerson = $users->map(function ($user) {
        return (Object) [
          'id'    => $user->id,
          'name' => $user->name,
          'count' => round(($user->id%7*20 + 150)/25),
        ]; });
      $mentorSkillsets = \App\Skill::whereIn('id', [1,14,22,3,13,19,25,16,26])->get()->map(function ($skill) {
        return (Object) [
          'id'    => $skill->id,
          'name' => $skill->name,
          'count' => round(($skill->id%7*20 + 150)/32),
        ]; });
      $seekingMentorship = $users->map(function ($user) {
        return (Object) [
          'id'    => $user->id,
          'name' => $user->name,
          'count' => round(($user->id%7*20 + 150)/61),
        ]; });
      $groupData = \App\Group::whereIn('id', [1,2,3,5,6])->get()->map(function ($skill) {
        return (Object) [
          'id'    => $skill->id,
          'name' => $skill->name,
          'count' => round(($skill->id%7*20 + 150)/32),
        ]; });
      $departmentBreakdown = \App\Department::whereIn('id', [1,2,3,5,7])->get()->map(function ($department) {
        return (Object) [
          'name' => $department->name,
          'count' => round(($department->id%7*20 + 150)/82),
        ]; });
      $skillsBreakdown = \App\Skill::whereIn('id', [1,14,22,3,13,19,25,16,26])->get()->map(function ($skill) {
        return (Object) [
          'id'    => $skill->id,
          'name' => $skill->name,
          'count' => round(($skill->id%7*20 + 150)/25),
        ]; });
      $keywordBreakdown = \App\Keyword::whereIn('id', [85+1,85+14,85+22,85+3,85+13,85+19,85+25,85+16,85+26,85+11,85+2,85+15,85+29])->get()->map(function ($skill) {
        return (Object) [
          'id'    => $skill->id,
          'name' => $skill->name,
          'count' => round(($skill->id%7*20 + 150)/95),
        ]; });

        return view('management.directreports')->with([
          'groupData' => $groupData,
          'messagesSent' => $messagesSent,
          'introductionsMade' => $introductionsMade,
          'introductionsRespondedTo' => $introductionsRespondedTo,
          'shoutoutsMade' => $shoutoutsMade,
          'shoutoutsReceived' => $shoutoutsReceived,
          'mentorBreakdown' => $mentorBreakdown,
          'skillsetsPerPerson' => $skillsetsPerPerson,
          'mentorSkillsets' => $mentorSkillsets,
          'seekingMentorship' => $seekingMentorship,
          'skillsBreakdown' => $skillsBreakdown,
          'departmentBreakdown' => $departmentBreakdown,
          'keywordBreakdown' => $keywordBreakdown,
        ]);
    }

    public function organization(Request $request)
    {
      $id = $request->user()->id;
      $users = User::whereIn('id', [$id + 1, $id + 35, $id + 24, $id + 16, $id + 19, $id + 22, $id + 3, $id + 2])->get();
      $groupData = $users->map(function ($user) {
        return (Object) [
          'id'    => $user->id,
          'name' => $user->name,
          'count' => round(($user->id + 150)/16),
        ]; });
      $messagesSent = $users->map(function ($user) {
        return (Object) [
          'id'    => $user->id,
          'name' => $user->name,
          'count' => round(($user->id + 150)/4),
        ]; });
      $introductionsMade = $users->map(function ($user) {
        return (Object) [
          'id'    => $user->id,
          'name' => $user->name,
          'count' => round(($user->id*20 + 150)/30),
        ]; });
      $introductionsRespondedTo = $users->map(function ($user) {
        return (Object) [
          'id'    => $user->id,
          'name' => $user->name,
          'count' => round(($user->id*20 + 150)/50),
        ]; });
      $shoutoutsMade = $users->map(function ($user) {
        return (Object) [
          'id'    => $user->id,
          'name' => $user->name,
          'count' => round(($user->id*20 + 150)/100),
        ]; });
      $shoutoutsReceived = $users->map(function ($user) {
        return (Object) [
          'id'    => $user->id,
          'name' => $user->name,
          'count' => round(($user->id%7*20 + 150)/45),
        ]; });
      $mentorBreakdown = collect([
        (Object) ['count' => ($users->count() - round($users->count()*.2))*22],
        (Object) ['count' => (round($users->count()*.2))*22]
      ]);
      $skillsetsPerPerson = $users->map(function ($user) {
        return (Object) [
          'id'    => $user->id,
          'name' => $user->name,
          'count' => round(($user->id%7*20 + 150)/8),
        ]; });
      $mentorSkillsets = \App\Skill::whereIn('id', [1,14,22,3,13,19,25,16,26])->get()->map(function ($skill) {
        return (Object) [
          'id'    => $skill->id,
          'name' => $skill->name,
          'count' => round(($skill->id%7*20 + 150)/8),
        ]; });
      $seekingMentorship = $users->map(function ($user) {
        return (Object) [
          'id'    => $user->id,
          'name' => $user->name,
          'count' => round(($user->id%7*20 + 150)/48),
        ]; });
      $groupData = \App\Group::whereIn('id', [1,2,3,5,6,7,8,9,10,16,17])->get()->map(function ($skill) {
        return (Object) [
          'id'    => $skill->id,
          'name' => $skill->name,
          'count' => round(($skill->id%7*20 + 150)/7),
        ]; });
      $departmentBreakdown = \App\Department::whereIn('id', [1,2,3,5,7])->get()->map(function ($department) {
        return (Object) [
          'name' => $department->name,
          'count' => round(($department->id%7*20 + 150)/22),
        ]; });
      $skillsBreakdown = \App\Skill::whereIn('id', [1,14,22,3,13,19,25,16,26])->get()->map(function ($skill) {
        return (Object) [
          'id'    => $skill->id,
          'name' => $skill->name,
          'count' => round(($skill->id%7*20 + 150)/5),
        ]; });
      $keywordBreakdown = \App\Keyword::whereIn('id', [85+1,85+14,85+22,85+3,85+13,85+19,85+25,85+16,85+26,85+11,85+2,85+15,85+29])->get()->map(function ($skill) {
        return (Object) [
          'id'    => $skill->id,
          'name' => $skill->name,
          'count' => round(($skill->id%7*20 + 150)/25),
        ]; });

      return view('management.organization')->with([
          'groupData' => $groupData,
          'messagesSent' => $messagesSent,
          'introductionsMade' => $introductionsMade,
          'introductionsRespondedTo' => $introductionsRespondedTo,
          'shoutoutsMade' => $shoutoutsMade,
          'shoutoutsReceived' => $shoutoutsReceived,
          'mentorBreakdown' => $mentorBreakdown,
          'skillsetsPerPerson' => $skillsetsPerPerson,
          'mentorSkillsets' => $mentorSkillsets,
          'seekingMentorship' => $seekingMentorship,
          'skillsBreakdown' => $skillsBreakdown,
          'departmentBreakdown' => $departmentBreakdown,
          'keywordBreakdown' => $keywordBreakdown,
      ]);
    }

    public function organizationForUser($id, Request $request)
    {
      $user = User::find($id);

      $users = User::whereIn('id', [$id + 1, $id + 35, $id + 24, $id + 16])->get();
      $groupData = $users->map(function ($user) {
        return (Object) [
          'id'    => $user->id,
          'name' => $user->name,
          'count' => round(($user->id + 150)/16),
        ]; });
      $messagesSent = collect([]);
      $introductionsMade = $users->map(function ($user) {
        return (Object) [
          'id'    => $user->id,
          'name' => $user->name,
          'count' => round(($user->id*20 + 150)/30),
        ]; });
      $introductionsRespondedTo = $users->map(function ($user) {
        return (Object) [
          'id'    => $user->id,
          'name' => $user->name,
          'count' => round(($user->id*20 + 150)/50),
        ]; });
      $shoutoutsMade = $users->map(function ($user) {
        return (Object) [
          'id'    => $user->id,
          'name' => $user->name,
          'count' => round(($user->id*20 + 150)/100),
        ]; });
      $shoutoutsReceived = $users->map(function ($user) {
        return (Object) [
          'id'    => $user->id,
          'name' => $user->name,
          'count' => round(($user->id%7*20 + 150)/45),
        ]; });
      $mentorBreakdown = collect([
        (Object) ['count' => ($users->count() - round($users->count()*.2))*6],
        (Object) ['count' => (round($users->count()*.2))*6]
      ]);
      $skillsetsPerPerson = $users->map(function ($user) {
        return (Object) [
          'id'    => $user->id,
          'name' => $user->name,
          'count' => round(($user->id%7*20 + 150)/8),
        ]; });
      $mentorSkillsets = \App\Skill::whereIn('id', [1,14,22,3,13,19,25,16,26])->get()->map(function ($skill) {
        return (Object) [
          'id'    => $skill->id,
          'name' => $skill->name,
          'count' => round(($skill->id%7*20 + 150)/8),
        ]; });
      $skillsBreakdown = collect();
      $seekingMentorship = $users->map(function ($user) {
        return (Object) [
          'id'    => $user->id,
          'name' => $user->name,
          'count' => round(($user->id%7*20 + 150)/48),
        ]; });
      $departmentBreakdown = collect([]);
      $keywordBreakdown = collect([]);

      return view('management.organization')->with([
          'user' => $user,
          'groupData' => $groupData,
          'messagesSent' => $messagesSent,
          'introductionsMade' => $introductionsMade,
          'introductionsRespondedTo' => $introductionsRespondedTo,
          'shoutoutsMade' => $shoutoutsMade,
          'shoutoutsReceived' => $shoutoutsReceived,
          'mentorBreakdown' => $mentorBreakdown,
          'skillsetsPerPerson' => $skillsetsPerPerson,
          'mentorSkillsets' => $mentorSkillsets,
          'seekingMentorship' => $seekingMentorship,
          'skillsBreakdown' => $skillsBreakdown,
          'departmentBreakdown' => $departmentBreakdown,
          'keywordBreakdown' => $keywordBreakdown,
      ]);
    }

    public function breakdownShoutoutsMadeIndex(Request $request)
    {
      $id = $request->user()->id;
      $users = User::whereIn('id', [$id + 1, $id + 35, $id + 24, $id + 16, $id + 19, $id + 22, $id + 3, $id + 2])->get();
      $results = $users->map(function ($user) {
        return (Object) [
          'id'    => $user->id,
          'name' => $user->name,
          'count' => round(($user->id*20 + 150)/100),
        ]; });

      return view('management.breakdowns.shoutouts.index')->with([
        'results' => $results,
      ]);
    }

    public function breakdownShoutoutsMadeShow(Request $request, $userId)
    {
      $results = \App\Shoutout::limit($request->count)->get();
      $user = User::find($userId);

      return view('management.breakdowns.shoutouts.show')->with([
        'results' => $results,
        'user' => $user,
      ]);
    }
}

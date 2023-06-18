<?php

namespace App\Http\Controllers\Group;

use App\Department;
use App\Group;
use App\Http\Controllers\Controller;
use App\Keyword;
use App\Option;
use App\Setting;
use App\Skill;
use App\Taxonomy;
use App\TitleUser;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['groupadmin', 'group', 'auth']);
    }

    public function demographics($slug)
    {
        $group = Group::where('slug', '=', $slug)->first();
        $this->checkIfReportingEnabled($group);

        $userIds = $group->users()->select('users.id')->pluck('id');

        $groupData = DB::table('groups')
                       ->whereNull('groups.deleted_at')
                       ->leftJoin('group_user', 'groups.id', '=', 'group_user.group_id')
                       ->whereIn('group_user.user_id', $userIds)
                       ->selectRaw('groups.name, count(*) as count')
                       ->groupBy('groups.name')
                       ->orderBy('count', 'desc')
                       ->get();
        
        $mentorBreakdown = DB::table('users')
                             ->whereIn('id', $userIds)
                             ->selectRaw('is_mentor, count(*) as count')
                             ->groupBy('is_mentor')
                             ->get();

        $taxonomies = Taxonomy::where('is_enabled', 1)->get();
        $taxonomies = $taxonomies->map(function ($taxonomy) use ($userIds) {
          $options = $taxonomy->options_with_users;
          $taxonomy->chartData = $options->map(function ($option) use ($userIds) {
            return [
              'name' => $option->name,
              'count' => $option->users()->whereIn('users.id', $userIds)->count(),
            ];
          })->reject(function ($option) {
            return $option['count'] == 0;
          })->sortByDesc('count');

          return $taxonomy;
        });

        $departmentBreakdown = DB::table('departments')
                                 ->selectRaw('departments.name, count(0) as count')
                                 ->groupBy('departments.name')
                                 ->leftJoin('users', 'departments.id', '=', 'users.department_id')
                                 ->whereIn('users.id', $userIds)
                                 ->orderBy('count', 'desc')
                                 ->limit(10)
                                 ->get();

        $introductionsMade = DB::table('introductions')
                               ->select('sent_by')
                               ->groupBy('sent_by')
                               ->whereIn('sent_by', $userIds)
                                     ->get()->count();

        $titles = \App\Title::all();

        $titles = $titles->map(function ($title) use ($userIds) {
          return (object) [
            'id' => $title->id,
            'name' => $title->name,
            'stats' => DB::table('title_user')
                            ->selectRaw('users.name, count(0) as count')
                            ->groupBy('users.name')
                            ->where('title_id', '=', $title->id)
                            ->join('users', 'title_user.assigned_id', '=', 'users.id')
                            ->whereIn('title_user.user_id', $userIds)
                            ->orderBy('count', 'desc')
                            ->limit(10)
                            ->get(),
          ];
        });

        return view('groups.reports.demographics')->with([
            'totalCount' => $userIds->count(),
            'group' => $group,
            'groupData' => $groupData,
            'taxonomies' => $taxonomies,
            'mentorBreakdown' => $mentorBreakdown,
            'departmentBreakdown' => $departmentBreakdown,
            'introductionsMade' => $introductionsMade,
            'titles' => $titles,
            'is_management_chain_enabled' => Setting::where('name', 'is_management_chain_enabled')->first()->value,
            'is_departments_enabled' => Setting::where('name', 'is_departments_enabled')->first()->value,
        ]);
    }

    public function behavior($slug)
    {
        $group = Group::where('slug', '=', $slug)->first();
        $this->checkIfReportingEnabled($group);

        $userIds = $group->users()->select('users.id')->pluck('id');

        $activity = DB::table('logs')->select(DB::raw('count(*) as count, DATE_FORMAT(created_at, "%b %e") as date'))
                             ->whereIn('user_id', $userIds)
                             ->groupBy('date')
                             ->where('created_at', '>', Carbon::parse('first day of january')->toDateTimeString())
                             ->get();


        return view('groups.reports.behavior')->with([
          'group' => $group,
          'activity' => $activity,
          'totalCount' => $userIds->count(),
        ]);
    }

    public function demographicsApi(Request $request)
    {
        if ($request->type == 'activity') {
          $response = DB::table('logs')->select(DB::raw('count(*) as count, DATE_FORMAT(created_at, "%b %e") as date'))
                             ->groupBy('date')
                             ->where('created_at', '>', Carbon::parse($request->start_date)->toDateTimeString())
                             ->where('created_at', '<', Carbon::parse($request->end_date)->toDateTimeString())
                             ->get();
        } elseif ($request->type == 'messages') {
          $response = DB::table('messages')->select(DB::raw('count(*) as count, DATE_FORMAT(created_at, "%b %e") as date'))
                             ->groupBy('date')
                             ->where('created_at', '>', Carbon::parse($request->start_date)->toDateTimeString())
                             ->where('created_at', '<', Carbon::parse($request->end_date)->toDateTimeString())
                             ->get();
        } elseif ($request->type == 'introductions') {
          $response = DB::table('introductions')->select(DB::raw('count(*) as count, DATE_FORMAT(created_at, "%b %e") as date'))
                             ->groupBy('date')
                             ->where('created_at', '>', Carbon::parse($request->start_date)->toDateTimeString())
                             ->where('created_at', '<', Carbon::parse($request->end_date)->toDateTimeString())
                             ->get();
        } elseif ($request->type == 'shoutouts') {
          $response = DB::table('shoutouts')->select(DB::raw('count(*) as count, DATE_FORMAT(created_at, "%b %e") as date'))
                             ->groupBy('date')
                             ->where('created_at', '>', Carbon::parse($request->start_date)->toDateTimeString())
                             ->where('created_at', '<', Carbon::parse($request->end_date)->toDateTimeString())
                             ->get();
        } elseif ($request->type == 'rsvps') {
          $response = DB::table('event_rsvps')->select(DB::raw('count(*) as count, DATE_FORMAT(created_at, "%b %e") as date'))
                             ->groupBy('date')
                             ->where('created_at', '>', Carbon::parse($request->start_date)->toDateTimeString())
                             ->where('created_at', '<', Carbon::parse($request->end_date)->toDateTimeString())
                             ->get();
        } elseif ($request->type == 'badges') {
          $response = DB::table('badge_user')->select(DB::raw('count(*) as count, DATE_FORMAT(created_at, "%b %e") as date'))
                             ->groupBy('date')
                             ->where('created_at', '>', Carbon::parse($request->start_date)->toDateTimeString())
                             ->where('created_at', '<', Carbon::parse($request->end_date)->toDateTimeString())
                             ->get();
        } elseif ($request->type == 'newusers') {
          $response = DB::table('users')->select(DB::raw('count(*) as count, DATE_FORMAT(created_at, "%b %e") as date'))
                             ->groupBy('date')
                             ->where('created_at', '>', Carbon::parse($request->start_date)->toDateTimeString())
                             ->where('created_at', '<', Carbon::parse($request->end_date)->toDateTimeString())
                             ->get();
        }

        $data = collect();
        $period = CarbonPeriod::create(Carbon::parse($request->start_date), Carbon::parse($request->end_date));
        foreach ($period as $date) {
          $data->put($date->format('M j'), 0);
        }
        $data = $data->map(function($item, $key) use ($response) {
          $day = $response->where('date', $key)->first();
          if ($day != null && $day->count != null)
            return $day->count;
          else
            return 0;
        });

        return ['dates' => $data->keys(), 'count' => $data->values()];
    }

    public function indexGroups($slug)
    {
      $group = Group::where('slug', '=', $slug)->first();
      $this->checkIfReportingEnabled($group);

      $userIds = $group->users()->select('users.id')->pluck('id');

      $groupData = DB::table('groups')
                       ->leftJoin('group_user', 'groups.id', '=', 'group_user.group_id')
                       ->whereIn('group_user.user_id', $userIds)
                       ->selectRaw('groups.id, groups.name, count(*) as count')
                       ->whereNull('deleted_at')
                       ->groupBy('groups.id')
                       ->orderBy('count', 'desc')
                       ->get();
        
        return view('groups.reports.breakdowns.groups.index')->with([
            'totalCount' => $groupData->count(),
            'group' => $group,
            'groups' => $groupData,
        ]);
    }

    public function indexDepartments($slug)
    {
      $group = Group::where('slug', '=', $slug)->first();
      $this->checkIfReportingEnabled($group);

      $userIds = $group->users()->select('users.id')->pluck('id');

      $departmentData = DB::table('departments')
                                 ->selectRaw('departments.id, departments.name, count(0) as count')
                                 ->groupBy('departments.id')
                                 ->leftJoin('users', 'departments.id', '=', 'users.department_id')
                                 ->whereIn('users.id', $userIds)
                                 ->whereNull('departments.deleted_at')
                                 ->orderBy('count', 'desc')
                                 ->get();

      return view('groups.reports.breakdowns.departments.index')->with([
            'totalCount' => $departmentData->count(),
            'group' => $group,
            'departments' => $departmentData,
        ]);
    }

    public function indexInterests($slug)
    {
      $group = Group::where('slug', '=', $slug)->first();
      $this->checkIfReportingEnabled($group);

      $userIds = $group->users()->select('users.id')->pluck('id');

      $keywordBreakdown = DB::table('keywords')
                              ->selectRaw('keywords.id, keywords.name, count(0) as count')
                              ->groupBy('keywords.id')
                              ->leftJoin('keyword_user', 'keywords.id', '=', 'keyword_user.keyword_id')
                              ->whereIn('keyword_user.user_id', $userIds)
                              ->orderBy('count', 'desc')
                              ->get();

      return view('groups.reports.breakdowns.keywords.index')->with([
        'totalCount' => $keywordBreakdown->count(),
        'group' => $group,
        'keywords' => $keywordBreakdown,
      ]);
    }

    public function indexMentors($slug)
    {
      $group = Group::where('slug', '=', $slug)->first();
      $this->checkIfReportingEnabled($group);

      $userIds = $group->users()->select('users.id')->pluck('id');

      $mentorBreakdown = DB::table('users')
                             ->whereIn('id', $userIds)
                             ->selectRaw('is_mentor, count(*) as count')
                             ->groupBy('is_mentor')
                             ->get();
      $mentorBreakdown = $mentorBreakdown->pluck('count', 'is_mentor');

      return view('groups.reports.breakdowns.mentors.index')->with([
        'totalCount' => $mentorBreakdown->count(),
        'group' => $group,
        'mentors' => $mentorBreakdown,
      ]);
    }

    public function indexSkillsets($slug)
    {
      $group = Group::where('slug', '=', $slug)->first();
      $this->checkIfReportingEnabled($group);

      $userIds = $group->users()->select('users.id')->pluck('id');

      $skillsBreakdown = DB::table('skills')
                              ->selectRaw('skills.id, skills.name, count(0) as count')
                              ->groupBy('skills.id')
                              ->leftJoin('skill_user', 'skills.id', '=', 'skill_user.skill_id')
                              ->whereIn('skill_user.user_id', $userIds)
                              ->orderBy('count', 'desc')
                              ->get();

      return view('groups.reports.breakdowns.skillsets.index')->with([
        'totalCount' => $skillsBreakdown->count(),
        'group' => $group,
        'skillsets' => $skillsBreakdown,
      ]);
    }

    public function indexIntroductions($slug)
    {
      $group = Group::where('slug', '=', $slug)->first();
      $this->checkIfReportingEnabled($group);

      $userIds = $group->users()->select('users.id')->pluck('id');

      $introductionsMade = DB::table('introductions')
                               ->select('sent_by')
                               ->groupBy('sent_by')
                               ->whereIn('sent_by', $userIds)
                               ->get()->count();
      $introductions = [
          0 => $userIds->count() - $introductionsMade,
          1 => $introductionsMade,
      ];

      return view('groups.reports.breakdowns.introductions.index')->with([
        'totalCount' => $userIds->count(),
        'introductions' => $introductions,
        'group' => $group,
      ]);
    }

    public function indexTitles($slug, $titleId)
    {
      $group = Group::where('slug', '=', $slug)->first();
      $this->checkIfReportingEnabled($group);

      $userIds = $group->users()->select('users.id')->pluck('id');

      $title = \App\Title::find($titleId);

      $users = DB::table('title_user')
                 ->selectRaw('users.id, users.name, count(0) as count')
                 ->groupBy('users.id')
                 ->where('title_id', '=', $title->id)
                 ->join('users', 'title_user.assigned_id', '=', 'users.id')
                 ->whereIn('title_user.user_id', $userIds)
                 ->orderBy('count', 'desc')
                 ->get();

      return view('groups.reports.breakdowns.titles.index')->with([
        'group' => $group,
        'title' => $title,
        'users' => $users,
        'totalCount' => $users->count(),
      ]);
    }

    public function showTitles($slug, $titleId, $userId)
    {
      $group = Group::where('slug', '=', $slug)->first();
      $this->checkIfReportingEnabled($group);

      $groupUserIds = $group->users()->select('users.id')->pluck('id');

      $title = \App\Title::find($titleId);
      $parentUser = User::find($userId);

      $users = User::whereHas('titles', function ($query) use ($titleId, $userId)  {
          $query->where('title_user.title_id', '=', $titleId)
                ->where('title_user.assigned_id', '=', $userId);
      })->whereIn('id', $groupUserIds)->get();

      return view('groups.reports.breakdowns.titles.show')->with([
        'group' => $group,
        'title' => $title,
        'parentUser' => $parentUser,
        'users' => $users,
        'totalCount' => $users->count(),
      ]);
    }

    public function showGroups($slug, $groupId)
    {
      $group = Group::where('slug', '=', $slug)->first();
      $this->checkIfReportingEnabled($group);

      $userIds = $group->users()->select('users.id')->pluck('id');
 
      $childGroup = Group::find($groupId);
      $groupUsers = $childGroup->users()->whereIn('users.id', $userIds)->orderBy('users.name', 'asc')->get();
      
      return view('groups.reports.breakdowns.groups.show')->with([
          'totalCount' => $groupUsers->count(),
          'group' => $group,
          'childGroup' => $childGroup,
          'groupUsers' => $groupUsers,
      ]);
    }

    public function showDepartments($slug, $departmentId)
    {
      $group = Group::where('slug', '=', $slug)->first();
      $this->checkIfReportingEnabled($group);

      $userIds = $group->users()->select('users.id')->pluck('id');

      $department = Department::find($departmentId);
      $departmentUsers = $group->users()->where('department_id', '=', $departmentId)->orderBy('users.name', 'asc')->get();

      return view('groups.reports.breakdowns.departments.show')->with([
          'totalCount' => $departmentUsers->count(),
          'group' => $group,
          'department' => $department,
          'departmentUsers' => $departmentUsers,
      ]);
    }

    public function showInterests($slug, $interestId)
    {
      $group = Group::where('slug', '=', $slug)->first();
      $this->checkIfReportingEnabled($group);

      $userIds = $group->users()->select('users.id')->pluck('id');

      $keyword = Keyword::find($interestId);
      $keywordUsers = $keyword->users()->whereIn('keyword_user.user_id', $userIds)->orderBy('users.name', 'asc')->get();

      return view('groups.reports.breakdowns.keywords.show')->with([
          'totalCount' => $keywordUsers->count(),
          'group' => $group,
          'keyword' => $keyword,
          'keywordUsers' => $keywordUsers,
      ]);
    }

    public function showSkillsets($slug, $skillsetId)
    {
      $group = Group::where('slug', '=', $slug)->first();
      $this->checkIfReportingEnabled($group);

      $userIds = $group->users()->select('users.id')->pluck('id');

      $skillset = Skill::find($skillsetId);
      $skillsetUsers = $skillset->users()->whereIn('skill_user.user_id', $userIds)->orderBy('users.name', 'asc')->get();

      return view('groups.reports.breakdowns.skillsets.show')->with([
          'totalCount' => $skillsetUsers->count(),
          'group' => $group,
          'skillset' => $skillset,
          'skillsetUsers' => $skillsetUsers,
      ]);
    }

    public function showMentors($slug, $mentorStatus)
    {
      $group = Group::where('slug', '=', $slug)->first();
      $this->checkIfReportingEnabled($group);

      $userIds = $group->users()->select('users.id')->pluck('id');

      $mentorUsers = $group->users()->where('is_mentor', '=', $mentorStatus)->orderBy('users.name', 'asc')->get();

      return view('groups.reports.breakdowns.mentors.show')->with([
        'totalCount' => $mentorUsers->count(),
        'group' => $group,
        'mentorStatus' => $mentorStatus == 0 ? 'not mentors' : 'mentors',
        'mentorUsers' => $mentorUsers,
      ]);
    }

    public function showIntroductions($slug, $introductionStatus)
    {
      $group = Group::where('slug', '=', $slug)->first();
      $this->checkIfReportingEnabled($group);

      $userIds = $group->users()->select('users.id')->pluck('id');

      $introductionsMade = DB::table('introductions')
                               ->select('sent_by')
                               ->groupBy('sent_by')
                               ->whereIn('sent_by', $userIds)
                               ->get()
                               ->pluck('sent_by');

      if ($introductionStatus == 1)
          $users = User::whereIn('id', $introductionsMade)->orderBy('name', 'asc')->get();
      else
          $users = User::whereIn('id', $userIds)->whereNotIn('id', $introductionsMade)->orderBy('name', 'asc')->get();

      return view('groups.reports.breakdowns.introductions.show')->with([
        'status' => $introductionStatus == 0 ? 'made an introduction' : 'didn\'t make an introduction',
        'users' => $users,
        'totalCount' => $users->count(),
        'group' => $group,
      ]);
    }

    public function indexTaxonomyOptions($slug, Taxonomy $taxonomy, Request $request)
    {
        $group = Group::where('slug', '=', $slug)->first();
        $this->checkIfReportingEnabled($group);

        $userIds = $group->users()->select('users.id')->pluck('id');

        $optionsBreakdown = $taxonomy->options_with_users->map(function ($option) use ($userIds) {
            return (Object) [
              'id' => $option->id,
              'name' => $option->name,
              'count' => $option->users()->whereIn('users.id', $userIds)->count(),
            ];
          })->reject(function ($option) {
            return $option->count == 0;
          })->sortByDesc('count');
        
        return view('groups.reports.breakdowns.taxonomies.index')->with([
            'totalCount' => $userIds->count(),
            'group' => $group,
            'taxonomy' => $taxonomy,
            'options' => $optionsBreakdown,
        ]);
    }

    public function showTaxonomyOption($slug, Taxonomy $taxonomy, Option $option, Request $request)
    {
        $group = Group::where('slug', '=', $slug)->first();
        $this->checkIfReportingEnabled($group);

        $userIds = $group->users()->select('users.id')->pluck('id');
        
        $optionUsers = $option->users()->whereIn('option_user.user_id', $userIds)->orderBy('users.name', 'asc')->get();

        return view('groups.reports.breakdowns.taxonomies.show')->with([
            'totalCount' => $userIds->count(),
            'group' => $group,
            'taxonomy' => $taxonomy,
            'option' => $option,
            'optionUsers' => $optionUsers,
        ]);
    }

    protected function checkIfReportingEnabled($group)
    {
      if ($group->is_reporting_enabled == 0)
          return back();
    }
   
}

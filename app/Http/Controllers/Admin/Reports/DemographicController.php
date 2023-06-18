<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Department;
use App\Group;
use App\Http\Controllers\Controller;
use App\Keyword;
use App\Option;
use App\Segment;
use App\Skill;
use App\Taxonomy;
use App\User;
use DB;
use Illuminate\Http\Request;

class DemographicController extends Controller
{
    public function indexGroups($id, Request $request)
    {
        $segment = Segment::find($id);
        $userIds = $segment->user_ids;

        $groupData = DB::table('groups')
                       ->whereNull('groups.deleted_at')
                       ->leftJoin('group_user', 'groups.id', '=', 'group_user.group_id')
                       ->whereIn('group_user.user_id', $userIds)
                       ->selectRaw('groups.id, groups.name, count(*) as count')
                       ->groupBy('groups.id')
                       ->orderBy('count', 'desc')
                       ->get();
        
        return view('admin.segments.breakdowns.groups.index')->with([
            'totalCount' => $userIds->count(),
            'segment' => $segment,
            'groups' => $groupData,
        ]);
    }

    public function showGroup($segmentId, $groupId, Request $request)
    {
        $segment = Segment::find($segmentId);
        $userIds = $segment->user_ids;

        $group = Group::find($groupId);
        $groupUsers = $group->users()->whereIn('group_user.user_id', $userIds)->orderBy('users.name', 'asc')->get();

        return view('admin.segments.breakdowns.groups.show')->with([
            'totalCount' => $userIds->count(),
            'segment' => $segment,
            'group' => $group,
            'groupUsers' => $groupUsers,
        ]);
    }

    public function indexDepartments($id, Request $request)
    {
        $segment = Segment::find($id);
        $userIds = $segment->user_ids;

        $departmentData = DB::table('departments')
                                 ->selectRaw('departments.id, departments.name, count(0) as count')
                                 ->whereNull('departments.deleted_at')
                                 ->groupBy('departments.id')
                                 ->leftJoin('users', 'departments.id', '=', 'users.department_id')
                                 ->whereIn('users.id', $userIds)
                                 ->orderBy('count', 'desc')
                                 ->get();

        return view('admin.segments.breakdowns.departments.index')->with([
            'totalCount' => $userIds->count(),
            'segment' => $segment,
            'departments' => $departmentData,
        ]);
    }

    public function showDepartment($segmentId, $departmentId, Request $request)
    {
        $segment = Segment::find($segmentId);
        $userIds = $segment->user_ids;

        $department = Department::find($departmentId);
        $departmentUsers = User::whereIn('id', $userIds)->where('department_id', $departmentId)->orderBy('users.name', 'asc')->get();

        return view('admin.segments.breakdowns.departments.show')->with([
            'totalCount' => $userIds->count(),
            'segment' => $segment,
            'department' => $department,
            'departmentUsers' => $departmentUsers,
        ]);
    }

    public function indexTaxonomyOptions(Segment $segment, Taxonomy $taxonomy, Request $request)
    {
        $userIds = $segment->user_ids;
        $optionsBreakdown = $taxonomy->options_with_users->map(function ($option) use ($userIds) {
            return (Object) [
              'id' => $option->id,
              'name' => $option->name,
              'count' => $option->users()->whereIn('users.id', $userIds)->count(),
            ];
          })->reject(function ($option) {
            return $option->count == 0;
          })->sortByDesc('count');
        
        return view('admin.segments.breakdowns.taxonomies.index')->with([
            'totalCount' => $userIds->count(),
            'segment' => $segment,
            'taxonomy' => $taxonomy,
            'options' => $optionsBreakdown,
        ]);
    }

    public function showTaxonomyOption(Segment $segment, Taxonomy $taxonomy, Option $option, Request $request)
    {
        $userIds = $segment->user_ids;
        $optionUsers = $option->users()->whereIn('option_user.user_id', $userIds)->orderBy('users.name', 'asc')->get();

        return view('admin.segments.breakdowns.taxonomies.show')->with([
            'totalCount' => $userIds->count(),
            'segment' => $segment,
            'taxonomy' => $taxonomy,
            'option' => $option,
            'optionUsers' => $optionUsers,
        ]);
    }

    public function indexMentors($id, Request $request)
    {
        $segment = Segment::find($id);
        $userIds = $segment->user_ids;

        $mentorBreakdown = DB::table('users')
                             ->whereIn('id', $userIds)
                             ->where('is_enabled', 1)
                             ->where('is_hidden', 0)
                             ->selectRaw('is_mentor, count(id) as count')
                             ->groupBy('is_mentor')
                             ->get();
        $mentorBreakdown = $mentorBreakdown->pluck('count', 'is_mentor');

        return view('admin.segments.breakdowns.mentors.index')->with([
            'totalCount' => $userIds->count(),
            'segment' => $segment,
            'mentors' => $mentorBreakdown,
        ]);
    }

    public function showMentor($id, $status, Request $request)
    {
        $segment = Segment::find($id);
        $userIds = $segment->user_ids;

        $users = User::whereIn('id', $userIds)->where('is_mentor', '=', $status)->orderBy('name', 'asc')->get();
    
        return view('admin.segments.breakdowns.mentors.show')->with([
            'totalCount' => $userIds->count(),
            'segment' => $segment,
            'status' => $status,
            'users' => $users,
        ]);
    }

    public function indexIntroductions($id, Request $request)
    {
        $segment = Segment::find($id);
        $userIds = $segment->user_ids;

        $introductionsMade = DB::table('introductions')
                               ->select('sent_by')
                               ->groupBy('sent_by')
                               ->whereIn('sent_by', $userIds)
                               ->get()->count();
        $introductions = [
            0 => $userIds->count() - $introductionsMade,
            1 => $introductionsMade,
        ];

        return view('admin.segments.breakdowns.introductions.index')->with([
            'totalCount' => $userIds->count(),
            'segment' => $segment,
            'introductions' => $introductions,
        ]);
    }

    public function showIntroduction($id, $status, Request $request)
    {
        $segment = Segment::find($id);
        $userIds = $segment->user_ids;

        $introductionsMade = DB::table('introductions')
                               ->select('sent_by')
                               ->groupBy('sent_by')
                               ->whereIn('sent_by', $userIds)
                               ->get()
                               ->pluck('sent_by');

        if ($status == 1)
            $users = User::whereIn('id', $introductionsMade)->get();
        else
            $users = User::whereIn('id', $userIds)->whereNotIn('id', $introductionsMade)->get();

        return view('admin.segments.breakdowns.introductions.show')->with([
            'totalCount' => $userIds->count(),
            'segment' => $segment,
            'status' => $status,
            'users' => $users,
        ]);
    }

    public function indexTitle($segmentId, $titleId, Request $request)
    {
        $segment = Segment::find($segmentId);
        $userIds = $segment->user_ids;

        $title = \App\Title::find($titleId);
        $users = DB::table('title_user')
                   ->selectRaw('users.id, users.name, count(0) as count')
                   ->groupBy('users.id')
                   ->where('title_id', '=', $title->id)
                   ->join('users', 'title_user.assigned_id', '=', 'users.id')
                   ->whereIn('users.id', $userIds)
                   ->orderBy('count', 'desc')
                   ->get();

        return view('admin.segments.breakdowns.titles.index')->with([
            'totalCount' => $userIds->count(),
            'segment' => $segment,
            'title' => $title,
            'users' => $users,
        ]);
    }

    public function showTitleBreakdown($segmentId, $titleId, $userId, Request $request)
    {
        $segment = Segment::find($segmentId);
        $userIds = $segment->user_ids;

        $title = \App\Title::find($titleId);
        $parentUser = User::find($userId);

        $users = User::whereHas('titles', function ($query) use ($titleId, $userId)  {
            $query->where('titles.id', '=', $titleId)
                  ->where('title_user.assigned_id', '=', $userId);
        })->get();

        return view('admin.segments.breakdowns.titles.show')->with([
            'totalCount' => $userIds->count(),
            'segment' => $segment,
            'title' => $title,
            'parentUser' => $parentUser, 
            'users' => $users,
        ]);
    }

}

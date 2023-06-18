<?php

namespace App\Http\Controllers\Admin;

use App\Export;
use App\Exports\SegmentExport;
use App\Jobs\Exports\SegmentExporter;
use App\Group;
use App\Jobs\Exports\UpdateCompletedExport;
use App\Http\Controllers\Controller;
use App\Segment;
use App\Setting;
use App\Taxonomy;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SegmentController extends Controller
{
    public function index()
    {
        return view('admin.segments.index')->with([
            'segments' => Segment::all(),
        ]);
    }

    public function show($id)
    {
        return redirect('/admin/segments/' . $id . '/demographics');
    }

    public function members(Request $request, $id)
    {
        $segment = Segment::find($id);

        $userIds = $segment->user_ids;

        if ($request->has('column') && $request->has('sort'))
            $members = User::withCount(['introductions', 'shoutouts', 'rsvps', 'ideations'])->whereIn('users.id', $userIds)->orderBy($request->column, $request->sort);
        else
            $members = User::withCount(['introductions', 'shoutouts', 'rsvps', 'ideations'])->whereIn('users.id', $userIds)->orderBy('name', 'asc');

        $members = $segment->applyFilters($members)->get()->unique();
        
        return view('admin.segments.members')->with([
            'members' => $members,
            'column' => $request->has('column') ? $request->input('column') : 'name',
            'sort' => $request->has('sort') ? $request->input('sort') : 'asc',
            'segment' => $segment,
            'totalCount' => $members->count(),
        ]);
    }

    public function demographics($id)
    {
        $segment = Segment::find($id);
        $userIds = $segment->user_ids;

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
                                 ->whereNull('departments.deleted_at')
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

        return view('admin.segments.demographics')->with([
            'totalCount' => $userIds->count(),
            'segment' => Segment::find($id),
            'groupData' => $groupData,
            'mentorBreakdown' => $mentorBreakdown,
            'departmentBreakdown' => $departmentBreakdown,
            'introductionsMade' => $introductionsMade,
            'taxonomies' => $taxonomies,
            'titles' => $titles,
        ]);
    }

    public function behavior($id)
    {
      $segment = Segment::find($id);
      $userIds = $segment->user_ids;

      $activity = DB::table('logs')->select(DB::raw('count(*) as count, DATE_FORMAT(created_at, "%b %e") as date'))
                             ->groupBy('date')
                             ->where('created_at', '>', Carbon::parse('first day of january')->toDateTimeString())
                             ->get();

      return view('admin.segments.behavior')->with([
          'segment' => $segment,
          'activity' => $activity,
          'totalCount' => $userIds->count(),
      ]);
    }

    public function create()
    {
        // $titles = \App\Title::orderBy('name', 'asc')->get();
        // $titles->map(function ($title) {
        //   return $title->options = \DB::table('title_user')
        //                 ->join('users', 'title_user.assigned_id', '=', 'users.id')
        //                 ->where('title_user.title_id', '=', $title->id)
        //                 ->distinct()
        //                 ->select('users.name')
        //                 ->orderBy('users.name', 'asc')
        //                 ->get()->pluck('name')->toArray();
        // });

        $groups = Group::whereNull('parent_group_id')->orderBy('name', 'asc')->get();

        return view('admin.segments.create')->with([
          'groups' => $groups,
        ]);
    }

    public function store(Request $request)
    {
      $request->validate([
        'name' => 'required|max:254',
        'description' => 'max:254',
        'start_date' => 'required',
        'end_date' => 'required',
      ]);

      $segment = Segment::create([
          'name' => $request->name,
          'description' => $request->description,
          'start_date' => $request->start_date,
          'end_date' => $request->end_date,
          'filters' => $request->only(['groups', 'filters']),
      ]);

      return redirect('/admin/segments/' . $segment->id);
    }

    public function edit($id, Request $request)
    {
        $segment = Segment::find($id);
        $titles = \App\Title::orderBy('name', 'asc')->get();
        $titles->map(function ($title) {
          return $title->options = \DB::table('title_user')
                        ->join('users', 'title_user.assigned_id', '=', 'users.id')
                        ->where('title_user.title_id', '=', $title->id)
                        ->distinct()
                        ->select('users.name')
                        ->orderBy('users.name', 'asc')
                        ->get()->pluck('name')->toArray();
        });

        if (isset($segment->filters->filters)) {
          $filters = $segment->filters->filters;
        } else {
          $filters = [];
        }

        return view('admin.segments.edit')->with([
            'segment' => $segment,
            'groups' => $groups = Group::whereNull('parent_group_id')->orderBy('name', 'asc')->get(),
            'titles' => $titles->pluck('options', 'name'),
            'filters' => json_encode($filters),
        ]);
    }

    public function update($id, Request $request)
    {
        Segment::find($id)->update([
          'name' => $request->name,
          'description' => $request->description,
          'filters' => $request->only('groups', 'filters'),
          'start_date' => $request->start_date,
          'end_date' => $request->end_date,
        ]);

        return redirect('/admin/segments/' . $id . '/demographics');
    }

    public function demographicsApi(Request $request)
    {
        $data = collect();
        $period = CarbonPeriod::create(Carbon::parse($request->start_date), Carbon::parse($request->end_date));
        foreach ($period as $date) {
          $data->put($date->format('M' . ($request->type == "monthlynewusers" ? '' : ' j')), 0);
        }

        if($request->has('segment'))
          $userIds = Segment::find($request->segment)->user_ids;
        if($request->has('group'))
          $userIds = Group::find($request->group)->users()->pluck('id');
        if ($request->type == 'activity') {
          $response = DB::table('logs')->select(DB::raw('count(*) as count, DATE_FORMAT(created_at, "%b %e") as date'));
          if(isset($userIds))
            $response = $response->whereIn('user_id', $userIds);
          $response = $response->groupBy('date')
                             ->where('created_at', '>=', Carbon::parse($request->start_date)->toDateTimeString())
                             ->where('created_at', '<=', Carbon::parse($request->end_date)->endOfDay()->toDateTimeString())
                             ->get();
        } elseif ($request->type == 'messages') {
          $response = DB::table('messages')->select(DB::raw('count(*) as count, DATE_FORMAT(created_at, "%b %e") as date'));
          if(isset($userIds))
            $response = $response->whereIn('sending_user_id', $userIds);
          $response = $response->groupBy('date')
                             ->where('created_at', '>=', Carbon::parse($request->start_date)->toDateTimeString())
                             ->where('created_at', '<=', Carbon::parse($request->end_date)->endOfDay()->toDateTimeString())
                             ->get();
        } elseif ($request->type == 'introductions') {
          $response = DB::table('introductions')->select(DB::raw('count(*) as count, DATE_FORMAT(created_at, "%b %e") as date'));
          if(isset($userIds))
            $response = $response->whereIn('sent_by', $userIds);
          $response = $response->groupBy('date')
                             ->where('created_at', '>=', Carbon::parse($request->start_date)->toDateTimeString())
                             ->where('created_at', '<=', Carbon::parse($request->end_date)->endOfDay()->toDateTimeString())
                             ->get();
        } elseif ($request->type == 'shoutouts') {
          $response = DB::table('shoutouts')->select(DB::raw('count(*) as count, DATE_FORMAT(created_at, "%b %e") as date'));
          if(isset($userIds))
            $response = $response->whereIn('shoutout_by', $userIds);
          $response = $response->groupBy('date')
                             ->where('created_at', '>=', Carbon::parse($request->start_date)->toDateTimeString())
                             ->where('created_at', '<=', Carbon::parse($request->end_date)->endOfDay()->toDateTimeString())
                             ->get();
        } elseif ($request->type == 'rsvps') {
          $response = DB::table('event_rsvps')->select(DB::raw('count(*) as count, DATE_FORMAT(created_at, "%b %e") as date'));
          if(isset($userIds))
            $response = $response->whereIn('user_id', $userIds);
          $response = $response->groupBy('date')
                             ->where('created_at', '>=', Carbon::parse($request->start_date)->toDateTimeString())
                             ->where('created_at', '<=', Carbon::parse($request->end_date)->endOfDay()->toDateTimeString())
                             ->get();
        } elseif ($request->type == 'badges') {
          $response = DB::table('badge_user')->select(DB::raw('count(*) as count, DATE_FORMAT(created_at, "%b %e") as date'));
          if(isset($userIds))
            $response = $response->whereIn('user_id', $userIds);
          $response = $response->groupBy('date')
                             ->where('created_at', '>=', Carbon::parse($request->start_date)->toDateTimeString())
                             ->where('created_at', '<=', Carbon::parse($request->end_date)->endOfDay()->toDateTimeString())
                             ->get();
        } elseif ($request->type == 'newusers') {
          $response = DB::table('users')->select(DB::raw('count(*) as count, DATE_FORMAT(created_at, "%b %e") as date'));
          if(isset($userIds))
            $response = $response->whereIn('users.id', $userIds);
          $response = $response->groupBy('date')
                             ->where('created_at', '>=', Carbon::parse($request->start_date)->toDateTimeString())
                             ->where('created_at', '<=', Carbon::parse($request->end_date)->endOfDay()->toDateTimeString())
                             ->get();
        } elseif($request->type == "monthlynewusers") {
            $response = DB::table('users')->select(DB::raw('count(*) as count, DATE_FORMAT(created_at, "%b") as date'));
          if(isset($userIds))
            $response = $response->whereIn('users.id', $userIds);
          $response = $response->groupBy("date")
                             ->where('created_at', '>=', Carbon::parse($request->start_date)->toDateTimeString())
                             ->where('created_at', '<=', Carbon::parse($request->end_date)->endOfDay()->toDateTimeString())
                             ->get();
        } elseif($request->type == 'totalusers') {
          $response = collect();
          foreach ($period as $date) {
            $totalUsersAtDate = DB::table('users');
            if(isset($userIds))
              $totalUsersAtDate = $totalUsersAtDate->whereIn('users.id', $userIds);

            $countAtDate = $totalUsersAtDate->where('created_at', '<=', $date->toDateTimeString())->count();
            $toPush = (object)[];
            $toPush->date = $date->format('M j');
            $toPush->count = $countAtDate;
            $response->push($toPush);
          }
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

    public function startExport(Request $request, $id)
    {
        $segment = Segment::find($id);

        $export = Export::create([
          'segment_id' => $id,
          'send_to' => $request->send_to_email,
        ]);

        $folder = $segment->getFolderSafeName() . ' - export';

        SegmentExporter::dispatch($segment, $folder, $export);

        return back()->with('success', "Export processing started. You'll receive an email in 15-20 minutes.");
    }

    public function downloadExport($exportId)
    {
        return response()->download(Export::find($exportId)->path);
    }

    public function checkIfExportCompleted($exportId)
    {
      return response(Export::find($exportId)->path);
    }

    public function delete($id)
    {
      $segment = Segment::find($id);
      $segment->delete();

      return redirect('/admin/segments');
    }
}

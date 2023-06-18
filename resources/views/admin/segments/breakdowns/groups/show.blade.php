@extends('admin.segments.layout')

@section('inner-page-content')
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/admin/segments/{{ $segment->id }}">Segment: {{ $segment->name }}</a></li>
        <li class="breadcrumb-item"><a href="/admin/segments/{{ $segment->id }}/demographics/groups">Groups Breakdown</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $group->name }}</li>
      </ol>
    </nav>

    <div class="row">
        <div class="col-md-12">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h5>Group {{ $group->name }} <i class="fas fa-angle-right"></i> Users in Segment <small>({{ $groupUsers->count() }} total users)</small></h5>
                
                @component('admin.segments.breakdowns.bulkadd', ['users' => $groupUsers])
                @endcomponent
            </div>
            <table class="table">
                <tr>
                    <td><b>user</b></td>
                    <td><b>job title</b></td>
                    <td></td>
                </tr>
                @foreach($groupUsers as $user)
                <tr>
                    <td>
                        <a href="/admin/users/{{ $user->id }}">{{ $user->name }}</a>
                    </td>
                    <td>{{ $user->job_title }}</td>
                    <td class="text-right">
                        <a href="/messages/new/?user={{ $user->id }}">Message</a>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection
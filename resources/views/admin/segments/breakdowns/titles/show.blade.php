@extends('admin.segments.layout')

@section('inner-page-content')
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/admin/segments/{{ $segment->id }}">Segment: {{ $segment->name }}</a></li>
        <li class="breadcrumb-item"><a href="/admin/segments/{{ $segment->id }}/demographics/titles/{{ $title->id }}">{{ $title->name }} Breakdown</a></li>
        <li class="breadcrumb-item active" aria-current="page">Users Reporting to {{ $parentUser->name }}</li>
      </ol>
    </nav>

    <div class="row">
        <div class="col-md-12">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-3">Users that have <a href="/admin/users/{{ $parentUser->id }}/">{{ $parentUser->name }}</a> assigned as their {{ $title->name }} <small>({{ $users->count() }} total users)</small></h5>
                
                @component('admin.segments.breakdowns.bulkadd', ['users' => $users])
                @endcomponent
            </div>
            <table class="table">
                <tr>
                    <td><b>user</b></td>
                    <td><b>job title</b></td>
                    <td></td>
                </tr>
                @foreach($users as $user)
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
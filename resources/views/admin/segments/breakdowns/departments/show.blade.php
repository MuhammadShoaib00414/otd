@extends('admin.segments.layout')

@section('inner-page-content')
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/admin/segments/{{ $segment->id }}">Segment: {{ $segment->name }}</a></li>
        <li class="breadcrumb-item"><a href="/admin/segments/{{ $segment->id }}/demographics/departments">Departments Breakdown</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $department->name }}</li>
      </ol>
    </nav>

    <div class="row">
        <div class="col-md-12">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h5>Department {{ $department->name }} <i class="fas fa-angle-right"></i> Users in Segment <small>({{ $departmentUsers->count() }} total users)</small></h5>
                
                @component('admin.segments.breakdowns.bulkadd', ['users' => $departmentUsers])
                @endcomponent
            </div>
            <table class="table">
                <tr>
                    <td><b>user</b></td>
                    <td><b>job title</b></td>
                    <td></td>
                </tr>
                @foreach($departmentUsers as $user)
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
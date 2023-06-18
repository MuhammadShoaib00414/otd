@extends('admin.segments.layout')

@section('inner-page-content')
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/admin/segments/{{ $segment->id }}">Segment: {{ $segment->name }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Departments Breakdown</li>
      </ol>
    </nav>

    <div class="row">
        <div class="col-md-12">
            <h5 class="mb-3">Breakdown of Departments in Segment <small>({{ $departments->count() }} total departments)</small></h5>
            <table class="table">
                <tr>
                    <td><b>department</b></td>
                    <td><b>total users</b></td>
                    <td></td>
                </tr>
                @foreach($departments as $department)
                <tr>
                    <td>
                        <a href="/admin/segments/{{ $segment->id }}/demographics/departments/{{ $department->id }}">{{ $department->name }}</a>
                    </td>
                    <td>
                        {{ $department->count }}
                    </td>
                    <td class="text-right">
                        <a href="/admin/segments/{{ $segment->id }}/demographics/departments/{{ $department->id }}">View users</a>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection
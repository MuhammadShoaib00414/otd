@extends('groups.layout')

@section('stylesheets')
@parent
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('body-class', 'col-md-8')

@section('inner-content')
    <div class="row">
        <div class="col-md-12">
            <h5 class="mb-3">Breakdown of Departments Your Members are in <small>({{ $departments->count() }} total departments)</small></h5>
            <div class="card">
                <table class="table">
                    <tr>
                        <td><b>department</b></td>
                        <td><b>total users</b></td>
                        @if($group->is_reporting_user_data_enabled)
                        <td></td>
                        @endif
                    </tr>
                    @foreach($departments as $department)
                    <tr>
                        <td>
                            @if($group->is_reporting_user_data_enabled)
                            <a href="/groups/{{ $group->slug }}/reports/demographics/departments/{{ $department->id }}">{{ $department->name }}</a>
                            @else
                                {{ $department->name }}
                            @endif
                        </td>
                        <td>
                            {{ $department->count }}
                        </td>
                        @if($group->is_reporting_user_data_enabled)
                        <td class="text-right">
                            <a href="/groups/{{ $group->slug }}/reports/demographics/departments/{{ $department->id }}">View users</a>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@endsection
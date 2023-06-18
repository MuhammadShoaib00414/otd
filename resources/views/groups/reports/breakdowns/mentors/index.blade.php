@extends('groups.layout')

@section('stylesheets')
@parent
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('body-class', 'col-md-8')

@section('inner-content')
    <div class="row">
        <div class="col-md-12">
            <h5 class="mb-3">Breakdown of Mentors in Your Group</h5>
            <div class="card">
                <table class="table">
                    <tr>
                        <td><b>mentor status</b></td>
                        <td><b>total users</b></td>
                        @if($group->is_reporting_user_data_enabled)
                        <td></td>
                        @endif
                    </tr>
                    <tr>
                        <td>
                            @if($group->is_reporting_user_data_enabled)
                                <a href="/groups/{{ $group->slug }}/reports/demographics/mentors/0">Mentor</a>
                            @else
                                Mentor
                            @endif
                        </td>
                        <td>
                            {{ $mentors[0] }}
                        </td>
                        @if($group->is_reporting_user_data_enabled)
                        <td class="text-right">
                            <a href="/groups/{{ $group->slug }}/reports/demographics/mentors/0">View users</a>
                        </td>
                        @endif
                    </tr>
                    <tr>
                        <td>
                            @if($group->is_reporting_user_data_enabled)
                                <a href="/groups/{{ $group->slug }}/reports/demographics/mentors/1">Not a Mentor</a>
                            @else
                                Not a mentor
                            @endif
                        </td>
                        <td>
                            {{ $mentors[1] }}
                        </td>
                        @if($group->is_reporting_user_data_enabled)
                        <td class="text-right">
                            <a href="/groups/{{ $group->slug }}/reports/demographics/mentors/1">View users</a>
                        </td>
                        @endif
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection
@extends('groups.layout')

@section('stylesheets')
@parent
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('body-class', 'col-md-8')

@section('inner-content')
    <div class="row">
        <div class="col-md-12">
            <h5 class="mb-3">Breakdown of Introductions in Your Group</h5>
            <div class="card">
                <table class="table mb-0">
                    <tr>
                        <td><b>introduction</b></td>
                        <td><b>total users</b></td>
                        @if($group->is_reporting_user_data_enabled)
                        <td></td>
                        @endif
                    </tr>
                    <tr>
                        <td>
                            @if($group->is_reporting_user_data_enabled)
                                <a href="/groups/{{ $group->slug }}/reports/demographics/introductions/0">Made an Introduction</a>
                            @else
                                Made an Introduction
                            @endif
                        </td>
                        <td>
                            {{ $introductions[0] }}
                        </td>
                        @if($group->is_reporting_user_data_enabled)
                        <td class="text-right">
                            <a href="/groups/{{ $group->slug }}/reports/demographics/introductions/0">View users</a>
                        </td>
                        @endif
                    </tr>
                    <tr>
                        <td>
                            @if($group->is_reporting_user_data_enabled)
                                <a href="/groups/{{ $group->slug }}/reports/demographics/introductions/1">Didn't Make an Introduction</a>
                            @else
                                Didn't Make an Introduction
                            @endif
                        </td>
                        <td>
                            {{ $introductions[1] }}
                        </td>
                        @if($group->is_reporting_user_data_enabled)
                        <td class="text-right">
                            <a href="/groups/{{ $group->slug }}/reports/demographics/introductions/1">View users</a>
                        </td>
                        @endif
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection
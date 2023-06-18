@extends('groups.layout')

@section('stylesheets')
@parent
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('body-class', 'col-md-8')

@section('inner-content')
    <div class="row">
        <div class="col-md-12">
            <h5 class="mb-3">Breakdown of Groups Your Members are in <small>({{ $groups->count() }} total groups)</small></h5>
            <div class="card">
                <table class="table">
                    <tr>
                        <td><b>group</b></td>
                        <td><b>total users</b></td>
                        @if($group->is_reporting_user_data_enabled)
                        <td></td>
                        @endif
                    </tr>
                    @foreach($groups as $childGroup)
                    <tr>
                        <td>
                            @if($group->is_reporting_user_data_enabled)
                                <a href="/groups/{{ $group->slug }}/reports/demographics/groups/{{ $childGroup->id }}">{{ $childGroup->name }}</a>
                            @else
                                {{ $childGroup->name }}
                            @endif
                        </td>
                        <td>
                            {{ $childGroup->count }}
                        </td>
                        @if($group->is_reporting_user_data_enabled)
                        <td class="text-right">
                            <a href="/groups/{{ $group->slug }}/reports/demographics/groups/{{ $childGroup->id }}">View users</a>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@endsection
@extends('groups.layout')

@section('stylesheets')
@parent
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('body-class', 'col-md-8')

@section('inner-content')
    <div class="row">
        <div class="col-md-12">
            <h5 class="mb-3">Breakdown of Your Members' {{ $title->name }}s <small>({{ $totalCount }} total users)</small></h5>
            <div class="card">
                <table class="table">
                    <tr>
                        <td><b>{{ $title->name }}</b></td>
                        <td><b>total users</b></td>
                        <td></td>
                    </tr>
                    @foreach($users as $user)
                    <tr>
                        <td>
                            <a href="/groups/{{ $group->slug }}/reports/demographics/titles/{{ $title->id }}/{{ $user->id }}">{{ $user->name }}</a>
                        </td>
                        <td>
                            {{ $user->count }}
                        </td>
                        <td class="text-right">
                            <a href="/groups/{{ $group->slug }}/reports/demographics/titles/{{ $title->id }}/{{ $user->id }}">View users</a>
                        </td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@endsection
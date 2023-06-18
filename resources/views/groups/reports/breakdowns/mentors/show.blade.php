@extends('groups.layout')

@section('stylesheets')
@parent
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('body-class', 'col-md-8')

@section('inner-content')

    <div class="row">
        <div class="col-md-12">
            <h5 class="mb-3">Users in your group that are {{ $mentorStatus }} <small>({{ $totalCount }} total users)</small></h5>
            <div class="card">
                <table class="table">
                    <tr>
                        <td><b>user</b></td>
                        <td><b>job title</b></td>
                        <td></td>
                    </tr>
                    @foreach($mentorUsers as $user)
                    <tr>
                        <td>
                            <a href="/users/{{ $user->id }}">{{ $user->name }}</a>
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
    </div>
@endsection
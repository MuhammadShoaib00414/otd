@extends('admin.users.layout')

@section('inner-page-content')

    <div class="row">
        <div class="col-md-3">
            <b>Points This Year</b><br>
            {{ $user->points_ytd }}</p>
        </div>

        <div class="col-md-3">
            <b>Total Points</b><br>
            {{ $user->points_total }}</p>
        </div>
        <div id="passwordResetContainer">
            <button id="passwordResetButton" type="submit" class="btn btn-primary btn-sm">Generate reset password link</button>
        </div>
    </div>

    <hr>

    <div class="row">
        @if($user->email)
        <div class="col-md-6">
            <b>Email</b><br>
            {{ $user->email }}</p>
        </div>
        @endif

        @if($user->job_title)
        <div class="col-md-6">
            <b>Job title</b><br>
            {{ $user->job_title }}</p>
        </div>
        @endif

        @if($user->company)
        <div class="col-md-6">
            <b>Company</b><br>
            {{ $user->company }}</p>
        </div>
        @endif

        @if($user->location)
        <div class="col-md-6">
            <b>Location</b><br>
            {{ $user->location }}</p>
        </div>
        @endif

        @if($user->twitter)
        <div class="col-md-6">
            <b>Twitter</b><br>
            {{ $user->twitter }}</p>
        </div>
        @endif

        @if($user->instagram)
        <div class="col-md-6">
            <b>Instagram</b><br>
            {{ $user->instagram }}</p>
        </div>
        @endif

        @if($user->facebook)
        <div class="col-md-6">
            <b>Facebook</b><br>
            {{ $user->facebook }}</p>
        </div>
        @endif

        @if($user->linkedin)
        <div class="col-md-6">
            <b>LinkedIn</b><br>
            {{ $user->linkedin }}</p>
        </div>
        @endif
    </div>

    <hr>

    <div>
        <b>Superpower</b><br>
        {{ $user->superpower }}</p>
    </div>

    <div>
        <b>Summary</b><br>
        {{ $user->summary }}</p>
    </div>

    <hr>
    @if(\App\Setting::where('name', 'is_management_chain_enabled')->first()->value)
        <table class="table">
            <tr>
                <th scope="col"><b>Management Chain</b></th>
                <th scope="col"><b>User</b></th>
            </tr>
            @foreach(App\Title::all() as $title)
            <tr>
                <td>{{ $title->name }}</td>
                <td>@if($user->titles->where('id', $title->id)->first())<a href="/admin/users/{{ $user->titles->where('id', $title->id)->first()->pivot->assigned->id }}">{{ $user->titles->where('id', $title->id)->first()->pivot->assigned->name }}</a>@endif</td>
            </tr>
            @endforeach
        </table>
    @endif
@endsection

@section('scripts')
    <script>
    $(function () {
        $('[data-toggle="popover"]').popover({
            trigger: 'focus'
        });
    });

    $('#passwordResetButton').click(function(e) {
        $.ajax('/admin/users/{{ $user->id }}/passwordResetLink',
            {
                success: function (data, status, xhr) 
                {
                    var url = data;
                    $('#passwordResetContainer').empty();
                    $('#passwordResetContainer').append('<input type="text" value="'+data+'">');
                    $('#passwordResetContainer').append('<p class="text-danger">This link will not be available after you leave this page.</p>');
                }
        });
    });
    </script>
@endsection
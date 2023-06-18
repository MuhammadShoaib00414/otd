@extends('admin.segments.layout')

@section('inner-page-content')

  <table class='table'>
    <thead>
      <tr>
        <th scope="col"><a href="?column=name&sort={{ ($sort == 'asc') ? 'desc' : 'asc' }}"><b>Name @if($column == 'name') @if($sort == 'desc')<i class="fas fa-caret-down"></i>@elseif($sort == 'asc')<i class="fas fa-caret-up"></i>@endif @endif</b></a></th>
        <th scope="col"><a href="?column=job_title&sort={{ ($sort == 'asc') ? 'desc' : 'asc' }}"><b>Job title @if($column == 'job_title') @if($sort == 'desc')<i class="fas fa-caret-down"></i>@elseif($sort == 'asc')<i class="fas fa-caret-up"></i>@endif @endif</b></a></th>
        <th scope="col" style="text-align: right;"><a href="?column=points_total&sort={{ ($sort == 'asc') ? 'desc' : 'asc' }}"><b>Points @if($column == 'points_total') @if($sort == 'desc')<i class="fas fa-caret-down"></i>@elseif($sort == 'asc')<i class="fas fa-caret-up"></i>@endif @endif</b></a></th>
        <th scope="col" style="text-align: right;"><a href="?column=introductions_count&sort={{ ($sort == 'asc') ? 'desc' : 'asc' }}"><b>Introductions @if($column == 'introductions_count') @if($sort == 'desc')<i class="fas fa-caret-down"></i>@elseif($sort == 'asc')<i class="fas fa-caret-up"></i>@endif @endif</b></a></th>
        <th scope="col" style="text-align: right;"><a href="?column=shoutouts_count&sort={{ ($sort == 'asc') ? 'desc' : 'asc' }}"><b>Shoutouts Given @if($column == 'shoutouts_count') @if($sort == 'desc')<i class="fas fa-caret-down"></i>@elseif($sort == 'asc')<i class="fas fa-caret-up"></i>@endif @endif</b></a></th>
        <th scope="col" style="text-align: right;"><a href="?column=rsvps_count&sort={{ ($sort == 'asc') ? 'desc' : 'asc' }}"><b>Events Attended @if($column == 'rsvps_count') @if($sort == 'desc')<i class="fas fa-caret-down"></i>@elseif($sort == 'asc')<i class="fas fa-caret-up"></i>@endif @endif</b></a></th>
        @if(getsetting('is_ideations_enabled'))
        <th scope="col" style="text-align: right;"><a href="?column=ideations_count&sort={{ ($sort == 'asc') ? 'desc' : 'asc' }}"><b>Ideations Joined @if($column == 'ideations_count') @if($sort == 'desc')<i class="fas fa-caret-down"></i>@elseif($sort == 'asc')<i class="fas fa-caret-up"></i>@endif @endif</b></a></th>
        @endif
        <th scope="col" style="text-align: right;"><a href="?column=is_mentor&sort={{ ($sort == 'desc') ? 'asc' : 'desc' }}"><b>Mentor @if($column == 'is_mentor') @if($sort == 'desc')<i class="fas fa-caret-down"></i>@elseif($sort == 'asc')<i class="fas fa-caret-up"></i>@endif @endif</b></a></th>
      </tr>
    </thead>
    @foreach($members as $user)
      <tr>
        <td><a href="/admin/users/{{ $user->id }}">{{ $user->name }}</a></td>
        <td>{{ $user->job_title }}</td>
        <td style="text-align: right;">{{ $user->points_total }}</td>
        <td style="text-align: right;">{{ $user->introductions_count }}</td>
        <td style="text-align: right;">{{ $user->shoutouts_count }}</td>
        <td style="text-align: right;">{{ $user->rsvps_count }}</td>
        @if(getsetting('is_ideations_enabled'))
          <td style="text-align: right;">{{ $user->ideations_count }}</td>
        @endif
        <td style="text-align: right;">@if($user->is_mentor)<i class="fas fa-check"></i>@endif</td>
      </tr>
    @endforeach
  </table>


@endsection
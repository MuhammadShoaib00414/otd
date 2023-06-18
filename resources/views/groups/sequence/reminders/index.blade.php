@extends('groups.layout')

@section('body-class', 'col-md-9')

@section('inner-content')
  <div class="d-flex justify-content-between align-items-center mb-2">
    <h3 class="font-weight-bold mb-0">Sequence Reminders</h3>
    <a href="/groups/{{ $group->slug }}/sequence/reminders/create" class="btn btn-sm btn-primary">New</a>
  </div>
  
    <div class="card">
      @if($reminders->count())
        <table class="table mb-0">
          <thead>
            <tr>
              <th scope="col"><b>Email subject</b></th>
              <th scope="col"><b>Send after</b></th>
              <th scope="col" class="text-center"><b>Enabled</b></th>
              <th scope="col"></th>
            </tr>
          </thead>
          <tbody>
            @foreach($reminders as $reminder)
              <tr>
                <td><a href="/groups/{{ $group->slug }}/sequence/reminders/{{ $reminder->id }}">{{ $reminder->subject }}</a></td>
                <td>{{ $reminder->send_after_days }} days</td>
                <td class="text-center text-muted">@if($reminder->is_enabled)<i class="fas fa-check"></i>@endif</td>
                <td class="text-right"><a href="/groups/{{ $group->slug }}/sequence/reminders/{{ $reminder->id }}">View</a></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @else
      <div class="card-body">
        <div class="my-4 text-center">
          <p>There are no reminders set up yet!</p>
          <a href="/groups/{{ $group->slug }}/sequence/reminders/create" class="btn btn-sm btn-primary">Create one</a>
        </div>
      </div>
      @endif
    </div>
@endsection
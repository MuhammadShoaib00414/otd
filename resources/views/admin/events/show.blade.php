@extends('admin.layout')

@section('page-content')
    <div class="d-flex justify-content-between align-items-center">
        <h5 class="m-0">{{ $event->name }}</h5>
        <div style="max-width:400px;" class="text-right d-inline-block">
            <a href="/admin/events/{{ $event->id }}/rsvp-export" class="btn btn-outline-dark btn-sm mr-2"><i class="fas fa-download"></i> Export Rsvps</a>
            <button class="btn btn-sm btn-primary mr-2" id="addUsersButton">Add users</button>
            <div class="d-none mb-3" id="addUsersForm">
                <form action="/admin/events/{{ $event->id }}/users/add" method="POST">
                    @csrf
                    @method('put')
                        <div class="d-inline-block">
                            <select name="userId" class="selectpicker" data-live-search="true">
                                @foreach($users as $user)
                                    <option data-token="{{ $user->name }}" value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select> 
                        </div>
                    <button @if($event->has_max_participants) onclick="return confirm('This event is at max participants. Are you sure you want to add this user?');" @endif type="submit" class="btn btn-sm btn-primary">Add</button>
                </form>
            </div>
            @if(!$event->trashed())
            <a href="/admin/events/{{ $event->id }}/edit" class="btn btn-sm btn-primary mr-2">Edit</a>
            <form action="/admin/events/{{ $event->id }}" method="post" class="d-inline-block mr-2">
                @method('delete')
                @csrf
                <button type="submit" class="btn btn-sm btn-light" id="deleteEvent">Delete</button>
            </form>
            @else
            <form action="/admin/events/{{ $event->id }}/restore" method="post" class="d-inline-block">
                @csrf
                <button type="submit" class="btn btn-sm btn-light mr-2">Restore</button>
            </form>
            @endif
        </div>
    </div>

    <hr>

    <div class="row">
        @if($event->image)
        <div class="col-md-6">
            <img src="{{ $event->image_path }}" style="width: 100%;">
        </div>
        @endif
        <div class="col-md-6">
            @if($event->trashed())
            <div class="mb-3">
                <span class="badge badge-secondary">Deleted on {{ $event->deleted_at->format('m/d/y - g:i a') }}</span>
            </div>
            @endif
            <b>Date</b>
            <p>{{ $event->date->tz(request()->user()->timezone)->format('m/d/y @ g:i a') }} @if($event->end_date)-  {{ $event->end_date->tz(request()->user()->timezone)->format('g:i a') }}@endif <small class="text-muted">({{ request()->user()->timezone}})</small></p>
            <hr>
            <b class="d-block mb-3">Details</b>
            <p>{!! nl2br($event->description) !!}</p>
        </div>
    </div>

    <hr>

    @if($event->allow_rsvps)
    <ul class="nav nav-tabs" id="myTab" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" id="yes-tab" data-toggle="tab" href="#yes" role="tab" aria-controls="home" aria-selected="true">RSVP: Yes ({{ $event->attending->count() }})</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="no-tab" data-toggle="tab" href="#no" role="tab" aria-controls="profile" aria-selected="false">RSVP: Interested ({{ $event->notAttending->count() }})</a>
      </li>
    </ul>
    <div class="tab-content p-3" id="myTabContent">
      <div class="tab-pane fade show active" id="yes" role="tabpanel" aria-labelledby="yes-tab">
        <div class="row">
            @foreach($event->attending as $user)
                <div class="col-md-3">
                    <a href="/admin/users/{{ $user->id }}" class="d-flex align-items-center mb-3">
                        <div style="height: 2em; width: 2em; border-radius: 50%; background-image: url('{{ $user->photo_path }}'); background-size: cover; background-position: center;"></div>
                        <span class="ml-2">{{ $user->name }}</span>
                    </a>
                </div>
            @endforeach
        </div>
      </div>
      <div class="tab-pane fade" id="no" role="tabpanel" aria-labelledby="no-tab">
        <div class="row">
            @foreach($event->notAttending as $user)
                <div class="col-md-3">
                    <a href="/admin/users/{{ $user->id }}" class="d-flex align-items-center mb-3">
                        <div style="height: 2em; width: 2em; border-radius: 50%; background-image: url('{{ $user->photo_path }}'); background-size: cover; background-position: center;"></div>
                        <span class="ml-2">{{ $user->name }}</span>
                    </a>
                </div>
            @endforeach
        </div>
      </div>
    </div>
    @endif
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/i18n/defaults-*.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
<script>
$(document).ready( function () {
  $('.selectpicker').selectpicker();

  $('#addUsersButton').click( function () {
    $('#addUsersButton').addClass('d-none');
    $('#addUsersForm').removeClass('d-none');
  });
});
</script>
  @if(!$event->trashed())
    <script>
      $('#deleteEvent').on('click', function(event) {
        event.preventDefault();
        if (confirm('Delete this event?'))
          $('#deleteEvent').parent().submit();
      });
    </script>
  @endif
@endsection
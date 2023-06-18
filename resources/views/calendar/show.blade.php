@extends('layouts.app')

@section('body-class', 'col-md-9')

@section('stylesheets')
@parent
<style>
  .add-to-calendar-checkbox {
    background-color: #fff;
    width: 100%;
    text-align: center;
    border: 1px solid #ced4da;
    color: #1a2b40;
    border-radius: .25rem;
    font-weight: bold;
    line-height: 2.4;
    display: block;
    margin-bottom: 1em;
  }
  .add-to-calendar-checkbox:hover {
    background-color: #e1ebf4;
    cursor: pointer;
  }
  .add-to-calendar-checkbox:checked ~ a {
    display: block;
    width: 100% !important;
    margin-left: 20px;
    margin-bottom: 0.5em;
  }
  .fc-event, .fc-event-dot {
      background-color: #1b2c41;
    }
    .fc-event {
        border: 1px solid #1b2c41;
    }
    .fc-list-heading-alt {
      margin-left: 0.5em;
    }
    .fc-list-item-title {
      word-break: break-word;
    }
    .fc-today-button {
      display: none;
    }
</style>
@endsection

@section('content')
<div class="main-container bg-lightest-brand">
  <div class="container-fluid pt-3">
    <div class="row">
      <div class="col-3">
        @include('partials.homepagenav')
      </div>
      <div class="col-6">
        <div class="card">
          <div class="card-body">
            <div class="row ml-1 mb-2" style="align-items: flex-start;">
              <h5 class="card-title">{{ $event->name }}</h5>
              @if($event->is_live)
                <div class="ml-3">
                  @include('groups.events.live')
                </div>
              @endif
            </div>
            <p>{{ $event->date->tz(request()->user()->timezone)->format('m/d/y @ g:i a') }} @if($event->end_date)<b>-</b> @if(!$event->end_date->isSameDay($event->date)) {{ $event->end_date->tz(request()->user()->timezone)->format('m/d/y @') }} @endif
              {{ $event->end_date->tz(request()->user()->timezone)->format('g:i a') }}
            @endif</p>
            @if($event->image)
            <div class="row">
              <img class="mx-md-auto" src="{{ $event->image_path }}" class="mb-3" style="max-width: 100%; max-height:600px;">
            </div>
            @endif
            <p style="font-size:1.1em;" class="mt-3 mb-1">{!! $event->formatted_body !!}</p>
            @if($event->custom_links)
            <hr>
            <div class="mt-3">
              @foreach($event->custom_links as $link)
                <div class="my-2 row justify-content-center">
                  <a style="overflow: hidden;" target="_blank" href="{{ $link['url'] }}" class="btn btn-outline-primary w-50">{{ $link['title'] }}<span class="sr-only"> @lang('calendar.for') {{ $event->name }}</span></a>
                </div>
              @endforeach
            </div>
            @endif
          </div>
        </div>
      </div>
      <div class="col-md-3">
        @if(!$event->has_happened && (!$event->allow_rsvps || ($userRsvp && $userRsvp->response == 'yes')))
          <div id="addToCalendarButton" class="mb-2">
          </div>
        @elseif($event->is_cancelled)
          <p class="alert alert-danger">@lang('events.This event has been cancelled'){{ $event->cancelled_reason ? ' due to ' : '' }}{{ $event->cancelled_reason }}.</p>
        @endif
        @if($authUser->is_admin && $event->allow_rsvps && $event->eventRsvps()->count())
          <button type="button" class="btn btn-primary w-100 mb-2" data-toggle="modal" data-target="#pickUsers">
            @lang('events.Messaging')
          </button>
        @endif
        @if($event->allow_rsvps && !$event->is_cancelled && !$event->has_happened)
          <p class="font-weight-bold">@lang('events.RSVP')</p>
          @if((!$userRsvp || $userRsvp->response == "") && !$userWaitlisted && !$event->has_max_participants)
            <div class="alert alert-light p-2" role="alert">
              <form action="/groups/{{ $event->group->slug }}/events/{{ $event->id }}/rsvp" method="post">
                @csrf
                <input type="hidden" name="rsvp" value="yes" />
                <button type="submit" class="d-block btn btn-primary mb-2 w-100">@lang('events.Im going')</button>
              </form>
              <form action="/groups/{{ $event->group->slug }}/events/{{ $event->id }}/rsvp" method="post">
                @csrf
                <input type="hidden" name="rsvp" value="no" />
                <button type="submit" class="d-block btn btn-outline-secondary w-100">@lang('events.Im interested')</button>
              </form>
            </div>
          @elseif($userRsvp && $userRsvp->response == 'yes')
            <div class="alert alert-light p-2" role="alert">
              <p><i class="icon-check"></i> @lang('events.Youre attending.')</p>
              <form action="/groups/{{ $event->group->slug }}/events/{{ $event->id }}/rsvp" method="post">
                @csrf
                <input type="hidden" name="rsvp" value="no" />
                <button type="submit" class="d-block btn btn-sm btn-outline-secondary w-100">@lang('events.Change to interested')</button>
              </form>
            </div>
          @elseif($userRsvp && $userRsvp->response == 'no')
            <div class="alert alert-light p-2" role="alert">
              <p><i class="icon-cross"></i> @lang('events.Youre interested.')</p>
              <form action="/groups/{{ $event->group->slug }}/events/{{ $event->id }}/rsvp" method="post">
                @csrf
                <input type="hidden" name="rsvp" value="yes" />
                <button type="submit" class="d-block btn btn-sm btn-outline-secondary w-100">@lang('events.Change to attending')</button>
              </form>
            </div>
          @elseif($event->has_max_participants && !$userWaitlisted)
            <div class="alert alert-light p-2" role="alert">
              <form action="/groups/{{ $event->group->slug }}/events/{{ $event->id }}/waitlist" method="post">
                @csrf
                <input type="hidden" name="waitlist" value="join" />
                <p><i class="icon-cross"></i><i style="font-weight: 700;">@lang('events.This event has max participants')</i>.</p>
                <button type="submit" class="d-block btn btn-sm btn-outline-secondary w-100">@lang('events.Join waitlist')</button>
              </form>
            </div>
          @elseif($userWaitlisted)
            <div class="alert alert-light p-2" role="alert">
              <p><i class="icon-cross"></i> @lang('events.Youre waitlisted.')</p>
              <form action="/groups/{{ $group->slug }}/events/{{ $event->id }}/waitlist" method="post">
                @csrf
                <input type="hidden" name="waitlist" value="leave" />
                <button type="submit" class="d-block btn btn-sm btn-outline-secondary w-100">@lang('events.Leave waitlist')</button>
              </form>
            </div>
          @endif
          <p><a href="#" data-toggle="modal" data-target="#attendingModal">@lang('events.Attending'):</a> {{ $event->attending->count() }}</p>
          @if($event->group->isUserAdmin($authUser->id) || request()->user()->is_admin)
            <p><a href="#" data-toggle="modal" data-target="#notAttendingModal">@lang('events.Interested'):</a> {{ $event->notAttending->count() }}</p>
          @else
            <p>@lang('events.Interested'):</a> {{ $event->notAttending->count() }}</p>
          @endif
          @if($event->groups()->count())
            <p><a href="#" data-toggle="modal" data-target="#invitedModal">@lang('events.Invited groups'):</a> {{ $event->groups()->count() }}</p>
          @endif
          @if($event->max_participants)
            <p>@lang('events.Max participants'): {{ $event->max_participants }}</p>
            <p>@lang('events.Waitlisted'): {{ $event->waitlist()->count() }}</p>
          @endif
        @endif

        @if($event->group->isUserAdmin($authUser->id) || request()->user()->is_admin)
          @if(!$event->is_cancelled)
            <hr>
          @endif
          <a href="/groups/{{ $event->group->slug }}/events/{{ $event->id }}/edit" class="d-block btn btn-secondary">@lang('calendar.edit-event')</a>
          
            <button data-toggle="modal" data-target="#cancelEventModal" class="btn btn-secondary-outline w-100 mt-2">
              @if($event->is_cancelled)
                @lang('calendar.undo-cancellation')
              @else
               @lang('calendar.cancel-event')
              @endif
             </button>

          <div class="modal fade" id="cancelEventModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <form action="/groups/{{ $event->group->slug }}/events/{{ $event->id }}/cancel" method="post">
                    @csrf
                    @method('put')
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">
                    @if($event->is_cancelled)
                      @lang('calendar.undo-cancellation')
                    @else
                     @lang('calendar.cancel-event')
                    @endif
                  </h5>

                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" value="{{ $event->is_cancelled ? 0 : 1 }}" name="is_cancelled">
                    @if(!$event->is_cancelled)
                      <label for="cancelled_reason">@lang('events.Reason for cancellation') <small class="text-muted">(@lang('general.optional'))</small></label>
                      <input type="text" class="form-control" name="cancelled_reason">
                    @else
                      <p>@lang('events.Are you sure you want to reinstate this event?')</p>
                    @endif
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('general.close')</button>
                  <button type="submit" class="btn btn-primary">@lang('general.save_changes')</button>
                </div>
                </form>
              </div>
            </div>
          </div>
          <form action="/groups/{{ $event->group->slug }}/events/{{ $event->id }}" method="post">
            @method('delete')
            @csrf
            <button type="submit" class="btn btn-light w-100 mt-2" id="deleteEvent">@lang('events.Delete Event')</button>
          </form>
        @endif
    </div>
  </div>
</div>

@if($event->group->isUserAdmin(request()->user()->id))
<div class="modal fade" id="pickUsers" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">@lang('calendar.create-group-message-with')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        @if($event->attending()->count())
          <div class="form-check">
            <input class="form-check-input" type="radio" name="gmType" id="attendingGM" value="attending">
            <label class="form-check-label" for="attendingGM">
              {{ $event->attending()->count() }} @lang('calendar.users-attending')
            </label>
          </div>
        @endif
        @if($event->notAttending()->count())
          <div class="form-check">
            <input class="form-check-input" type="radio" name="gmType" id="notAttendingGM" value="notAttending">
            <label class="form-check-label" for="notAttendingGM">
              {{ $event->notAttending()->count() }} @lang('calendar.users-not-attending')
            </label>
          </div>
        @endif
        @if($event->waitlist()->count())
          <div class="form-check">
            <input class="form-check-input" type="radio" name="gmType" id="waitlistGM" value="waitlist">
            <label class="form-check-label" for="waitlistGM">
              {{ $event->waitlist()->count() }} @lang('calendar.users-on-waitlist')
            </label>
          </div>
        @endif
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('general.close')</button>
        <button onclick="getNextModal()" type="button" class="btn btn-primary">@lang('general.next')</button>
      </div>
    </div>
  </div>
</div>
@endif

<div class="modal fade" id="attendingModal" tabindex="-1" role="dialog" aria-labelledby="attendingModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="attendingModalScrollableTitle">@lang('calendar.attending-modal-title')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          @foreach($event->attending as $user)
            <div class="col-md-6">
                <a href="/users/{{ $user->id }}" class="d-flex align-items-center mb-3 font-dark light-hover-bg p-1">
                    <div style="height: 3em; width: 3em; border-radius: 50%; background-image: url('{{ $user->photo_path }}'); background-size: cover; background-position: center;"></div>
                    <div class="ml-2">
                      <span class="d-block font-weight-bold">{{ $user->name }}</span>
                      <span class="d-block">{{ $user->job_title }}</span>
                    </div>
                </a>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>

@if($event->groups()->count())
  <div class="modal fade" id="invitedModal" tabindex="-1" role="dialog" aria-labelledby="invitedModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="attendingModalScrollableTitle">@lang('calendar.invited-groups') <small>(@lang('general.created_by') {{ $event->group->name }})</small></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        @foreach($event->groups as $groupName)
          <div class="row mt-1">
            <div class="col-md-6">
                <p style="font-size:1.2em">{{ $groupName->name }}</p>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endif

@if($event->group->isUserAdmin($authUser->id) || request()->user()->is_admin)

  <div class="modal fade" id="notAttendingModal" tabindex="-1" role="dialog" aria-labelledby="notAttendingModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="notAttendingModalScrollableTitle">@lang('calendar.interested-in-attending')</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            @foreach($event->notAttending as $user)
              <div class="col-md-6">
                  <a href="/users/{{ $user->id }}" class="d-flex align-items-center mb-3 font-dark light-hover-bg p-1">
                      <div style="height: 3em; width: 3em; border-radius: 50%; background-image: url('{{ $user->photo_path }}'); background-size: cover; background-position: center;"></div>
                      <div class="ml-2">
                        <span class="d-block font-weight-bold">{{ $user->name }}</span>
                        <span class="d-block">{{ $user->job_title }}</span>
                      </div>
                  </a>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="groupMessageModal" tabindex="-1" role="dialog" aria-labelledby="groupMessageModal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="notAttendingModalScrollableTitle">@lang('calendar.create-group-message')</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form method="GET" action="/messages/new" id="groupMessageForm">

            <button class="btn btn-primary">@lang('general.create')</button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endif
@endsection

@section('scripts')
  @if($event->group->isUserAdmin($authUser->id))
    <script>
      $('#deleteEvent').on('click', function(event) {
        event.preventDefault();
        if (confirm('Delete this event?'))
          $('#deleteEvent').parent().submit();
      });

      $( "#groupMessageForm" ).submit(function( event ) {
        //event.preventDefault();
        var selected = $("#groupMessageForm").find('input[name="type"]:checked').val();
        var toAdd = [];
        if(selected == "attending")
        {
          var users = @json($event->attending()->pluck("users.id"));
          users.forEach((userId) => {
            toAdd.push($("<input />").attr("type", "hidden")
            .attr("name", "recipients[]")
            .attr("value", userId));
          });
        }
        else if(selected == "interested")
        {
          var users = @json($event->notAttending()->pluck("users.id"));
          users.forEach((userId) => {
            toAdd.push($("<input />").attr("type", "hidden")
            .attr("name", "recipients[]")
            .attr("value", userId));
          });
        }
        else if(selected == "waitlist")
        {
          var users = @json($event->waitlist()->pluck("id"));
          console.log(users);
          users.forEach((userId) => {
            toAdd.push($("<input />").attr("type", "hidden")
            .attr("name", "recipients[]")
            .attr("value", userId));
          });
        }
        toAdd.forEach((user) => {
          user.appendTo('#groupMessageForm');
        });
      });


    </script>
  @endif
  <script type="text/javascript" src="/js/ouical.min.js"></script>
  <script>
    function getNextModal()
    {
      var selected = $("input[name='gmType']:checked").val();
      if(selected == 'attending')
      {
        var users = JSON.parse('{{ json_encode($event->attending()->select("users.name", "users.id")->get()->toArray()) }}'.replace(/&quot;/g,'"'));
      }
      else if(selected == 'notAttending')
      {
        var users = JSON.parse('{{ json_encode($event->notAttending()->select("users.name", "users.id")->get()->toArray()) }}'.replace(/&quot;/g,'"'));
      }
      else if(selected == 'waitlist')
      {
        var users = JSON.parse('{{ json_encode($event->waitlist_users->map(function($user) { return $user->only(["name", "id"]); })->toArray()) }}'.replace(/&quot;/g,'"'));
      }
      $('#pickUsers').modal('hide');

      $('#groupMessageForm > div').remove();

      users.forEach(function (user) {
        $('#groupMessageForm').prepend(`<div class="form-check">
                                          <input checked name="users[]" class="form-check-input" type="checkbox" value="`+ user.id +`" id="check`+ user.id +`">
                                          <label class="form-check-label" for="check`+ user.id +`">
                                            `+ user.name +`
                                          </label>
                                      </div>`);

      $('#groupMessageModal').modal('show');
      });

    }
    var myCalendar = createCalendar({
      options: {
        class: 'addToCalendarButton',
      },
      data: {
        // Event title
        title: '{{ $event->name }}',
        // Event start date
        start: new Date('{{ $event->date->tz(request()->user()->timezone)->format('F j, Y G:i') }}'),
        // Event duration (IN MINUTES)
        duration: @if($event->end_date)
                    {{ $event->date->tz(request()->user()->timezone)->diffInMinutes($event->end_date->tz(request()->user()->timezone)) }}
                  @else
                    60
                  @endif,
        // Event Description
        description: '{{ trim(preg_replace("/\s+/", " ", $event->description)) }}'
      }
    });

    document.querySelector('#addToCalendarButton').appendChild(myCalendar);
  </script>
@endsection
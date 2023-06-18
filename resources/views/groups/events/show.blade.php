@extends('groups.layout')

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
  .spinner {
    border: 5px solid #f3f3f3;
    border-top: 5px solid {{ getThemeColors()->primary['200'] }};
    border-radius: 50%;
    width: 50px;
    height: 50px;
    margin: auto;
    animation: spin 2s linear infinite;
  }

  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }

  .modal-dialog.zoom {
    width: 84%;
    max-width: none;
    height: 91%;
    margin: auto;
    /*top: 6%;*/
  }
  
  .modal-content.zoom {
    height: 100%;
    border: 0;
  }

  .zoom-sm {
    height: 75% !important;
    width: 42% !important;
  }
  
  .modal-body.zoom {
    overflow-y: scroll;
    padding: 0;
  }

  #zoom_iframe {
    width: 100%;
    height: 100%;
    border: none;
    overflow: scroll;
  }
</style>
@endsection

@section('inner-content')
<div class="row mt-3">
  <div class="col-md-8">
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
              <a style="overflow: hidden;" target="_blank" href="{{ $link['url'] }}" class="btn btn-outline-primary w-50 font-size-sm-sm">{{ $link['title'] }}<span class="sr-only"> for {{ $event->name }}</span></a>
            </div>
          @endforeach
        </div>
        @endif
      </div>
    </div>
  </div>
  <div class="col-md-4">
    @if(!$event->has_happened && $event->zoom_meeting_id && is_zoom_enabled())
      <button id="join_zoom_meeting_button" data-toggle="modal" data-target="#zoom_meeting" class="btn btn-primary w-100 mb-2">@lang('events.Join zoom meeting')</button>
    @endif
    @if(!$event->has_happened && (!$event->allow_rsvps || ($userRsvp && $userRsvp->response == 'yes')))
      <div id="addToCalendarButton" class="mb-2">
      </div>
    @elseif($event->is_cancelled)
      <p class="alert alert-danger">@lang('events.This event has been cancelled'){{ $event->cancelled_reason ? ' due to ' : '' }}{{ $event->cancelled_reason }}.</p>
    @endif
    @if(($event->group->isUserAdmin(request()->user()->id) || request()->user()->is_admin) && $event->allow_rsvps && $event->eventRsvps()->count())
      <button type="button" class="btn btn-primary w-100 mb-2" data-toggle="modal" data-target="#pickUsers">
        Messaging
      </button>
    @endif
    @if($event->allow_rsvps && !$event->is_cancelled && !$event->has_happened)
      <p class="font-weight-bold">@lang('events.RSVP')</p>
      @if(!$userRsvp && !$userWaitlisted && !$event->has_max_participants)
        <div class="alert alert-light p-2" role="alert">
          <form action="/groups/{{ $group->slug }}/events/{{ $event->id }}/rsvp" method="post">
            @csrf
            <input type="hidden" name="rsvp" value="yes" />
            <button type="submit" class="d-block btn btn-primary mb-2 w-100">@lang('events.Im going')</button>
          </form>
          <form action="/groups/{{ $group->slug }}/events/{{ $event->id }}/rsvp" method="post">
            @csrf
            <input type="hidden" name="rsvp" value="no" />
            <button type="submit" class="d-block btn btn-outline-secondary w-100">@lang('events.Im interested')</button>
          </form>
        </div>
      @elseif($userRsvp && $userRsvp->response == 'yes')
        <div class="alert alert-light p-2" role="alert">
          <p><i class="icon-check"></i>@lang('events.Youre attending.')</p>
          <form action="/groups/{{ $group->slug }}/events/{{ $event->id }}/rsvp" method="post">
            @csrf
            <input type="hidden" name="rsvp" value="no" />
            <button type="submit" class="d-block btn btn-sm btn-outline-secondary w-100">@lang('events.Change to interested')</button>
          </form>
        </div>
      @elseif($userRsvp && $userRsvp->response == 'no' && !$event->has_max_participants)
        <div class="alert alert-light p-2" role="alert">
          <p><i class="icon-cross"></i> @lang('events.Youre interested.')</p>
          <form action="/groups/{{ $group->slug }}/events/{{ $event->id }}/rsvp" method="post">
            @csrf
            <input type="hidden" name="rsvp" value="yes" />
            <button type="submit" class="d-block btn btn-sm btn-outline-secondary w-100">@lang('events.Change to attending')</button>
          </form>
        </div>
      @elseif($event->has_max_participants && !$userWaitlisted)
        <div class="alert alert-light p-2" role="alert">
          <form action="/groups/{{ $group->slug }}/events/{{ $event->id }}/waitlist" method="post">
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
      <p><a href="#" data-toggle="modal" data-target="#attendingModal">@lang('events.Attending'):</a> {{ $event->attending()->disableCache()->count() }}</p>
      @if($group->isUserAdmin($authUser->id) || request()->user()->is_admin)
        <p><a href="#" data-toggle="modal" data-target="#notAttendingModal">@lang('events.Interested'):</a> {{ $event->notAttending()->disableCache()->count() }}</p>
      @else
        <p>@lang('events.Interested'):</a> {{ $event->notAttending()->disableCache()->count() }}</p>
      @endif
      @if($event->groups()->count())
        <p><a href="#" data-toggle="modal" data-target="#invitedModal">@lang('events.Invited groups'):</a> {{ $event->groups()->count() }}</p>
      @endif
      @if($event->max_participants)
        <p>@lang('events.Max participants'): {{ $event->max_participants }}</p>
        <p>@lang('events.Waitlisted'): {{ $event->waitlist()->count() }}</p>
      @endif
    @endif

    @if($group->isUserAdmin($authUser->id) || request()->user()->is_admin || $event->created_by == request()->user()->id)
      @if(!$event->is_cancelled)
        <hr>
      @endif
      @if($event->eventRsvps()->where('response', 'yes')->exists())
        <a href="/groups/{{ $group->slug }}/events/{{ $event->id }}/rsvp-export" class="d-block btn btn-outline-secondary mb-2"><i class="fas fa-download"></i> @lang('events.Export Rsvps')</a>
      @endif

      @if($event->group->id == $group->id)
        <a href="/groups/{{ $group->slug }}/events/{{ $event->id }}/edit" class="d-block btn btn-secondary">@lang('events.Edit Event')</a>
      @endif
      @if(!$event->is_cancelled)
      <div data-toggle="tooltip" data-placement="top" title="If you cancel this event, all users who RSVP'd will be notified">
        <button data-toggle="modal" data-target="#cancelEventModal" class="btn btn-secondary-outline w-100 mt-2">@lang('events.Cancel Event')</button>
      </div>
      @endif

      <div class="modal fade" id="cancelEventModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <form action="/groups/{{ $group->slug }}/events/{{ $event->id }}/cancel" method="post">
                @csrf
                @method('put')
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">{{ $event->is_cancelled ? __('events.Undo Cancellation') : __('Cancel Event') }}</h5>
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
      <form action="/groups/{{ $group->slug }}/events/{{ $event->id }}" method="post">
        @method('delete')
        @csrf
        <button type="submit" class="btn btn-light w-100 mt-2" id="deleteEvent" data-toggle="tooltip" data-placement="top" title="If you delete this event, anyone who RSVP'd will not be notified">@lang('events.Delete Event')</button>
      </form>
    @endif
</div>

@if($event->group->isUserAdmin(request()->user()->id))
<div class="modal fade" id="pickUsers" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">@lang('events.Create group message with...')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        @if($event->attending()->count())
          <div class="form-check">
            <input class="form-check-input" type="radio" name="gmType" id="attendingGM" value="attending">
            <label class="form-check-label" for="attendingGM">
              {{ $event->attending()->count() }} users attending
            </label>
          </div>
        @endif
        @if($event->notAttending()->count())
          <div class="form-check">
            <input class="form-check-input" type="radio" name="gmType" id="notAttendingGM" value="notAttending">
            <label class="form-check-label" for="notAttendingGM">
              {{ $event->notAttending()->count() }} @lang('events.users not attending')
            </label>
          </div>
        @endif
        @if($event->waitlist()->count())
          <div class="form-check">
            <input class="form-check-input" type="radio" name="gmType" id="waitlistGM" value="waitlist">
            <label class="form-check-label" for="waitlistGM">
              {{ $event->waitlist()->count() }} @lang('events.users on the waitlist')
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
        <h5 class="modal-title" id="attendingModalScrollableTitle">@lang('events.Attending (RSVPd Yes)')</h5>
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
        <h5 class="modal-title" id="attendingModalScrollableTitle">@lang('events.Invited Groups') <small>(@lang('events.created by') {{ $event->group->name }})</small></h5>
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

@if($group->isUserAdmin($authUser->id) || request()->user()->is_admin)

  <div class="modal fade" id="notAttendingModal" tabindex="-1" role="dialog" aria-labelledby="notAttendingModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="notAttendingModalScrollableTitle">@lang('events.Interested in attending')</h5>
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
          <h5 class="modal-title" id="notAttendingModalScrollableTitle">@lang('events.Create group message')</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form method="GET" action="/messages/new" id="groupMessageForm">
            <input type="hidden" name="createIndividually" value="true">
            <button class="btn btn-primary">@lang('general.create')</button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endif
@if(!$event->has_happened && $event->zoom_meeting_id)
<div class="modal fade" id="zoom_meeting" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 10000000;">
  <div class="modal-dialog zoom modal-dialog-scrollable" role="document">
    <div class="modal-content zoom" id="zoomModal">
      <div class="modal-header">
        <div class="w-100 d-flex justify-content-between align-items-center">
          <a href="#" id="changeSize"><i class="far fa-window-maximize"></i></a>
          <a href="{{ $event->zoom_invite_link }}" target="_blank" class="btn btn-primary btn-sm">Join on zoom app</a>
        </div>
      </div>
      <div class="modal-body zoom d-flex justify-content-center align-items-center" id="zoom_meeting_modal_body">
        <div class="spinner" id="zoom_loading_spinner"></div>
        <iframe id="zoom_iframe" src="" class="d-none h-100 b-0" style="overflow: scroll;" scrolling="yes">
        </iframe>
      </div>
    </div>
  </div>
</div>
@endif
@endsection

@section('scripts')
<script>
  $('#join_zoom_meeting_button').click(function() {
    if(!$('#zoom_iframe').attr('src'))
    {
      $('#zoom_iframe').attr('src', '/zoom/{{ $event->zoom_meeting_id }}?pwd={{ $event->zoom_meeting_password }}');
      addEventListener("beforeunload", beforeUnloadListener, {capture: true});
    }
  }); 

  const beforeUnloadListener = (event) => {
    event.preventDefault();
    return event.returnValue = "Are you sure you want to exit?";
  };

  $('#changeSize').click(function(e) {
    e.preventDefault();
    $('#zoomModal').toggleClass('zoom-sm');
  });

  $('#zoom_iframe').on('load', function() {
    if($('#zoom_loading_spinner').hasClass('d-none'))
    {
      removeEventListener("beforeunload", beforeUnloadListener, {capture: true});
      window.location.reload();
    }
    $('#zoom_loading_spinner').addClass('d-none');
    $('#zoom_iframe').removeClass('d-none');
    $('#zoom_iframe').addClass('d-block');
  });


  @if($group->isUserAdmin($authUser->id))
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
  @endif
  </script>
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
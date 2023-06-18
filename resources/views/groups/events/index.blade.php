@extends('groups.layout')

@section('stylesheets')
@parent
  <link rel="stylesheet" type="text/css" href="https://unpkg.com/@fullcalendar/core@4.2.0/main.min.css">
  <link rel="stylesheet" type="text/css" href="https://unpkg.com/@fullcalendar/daygrid/main.min.css">
  <link rel="stylesheet" type="text/css" href="https://unpkg.com/@fullcalendar/list/main.min.css">
  <style>
    .fc-event, .fc-event-dot {
      background-color: #1b2c41;
    }
    .fc-event {
        border: 1px solid #1b2c41;
    }
    .fc-list-heading-alt {
      margin-left: 0.5em;
    }
    .fc-today-button {
      display: none;
    }
    .fc-button-primary {
      background-color: {{ getThemeColors()->accent['300'] }};
      border: 1px solid {{ getThemeColors()->accent['400'] }};
    }
    .fc-button-primary:hover {
      background-color: {{ getThemeColors()->accent['400'] }};
      border-color: {{ getThemeColors()->accent['400'] }}!important;
    }
    .fc-button-active {
      background-color: {{ getThemeColors()->accent['400'] }}!important;
      border-color: {{ getThemeColors()->accent['400'] }}!important;
    }
    /* td.fc-list-item-marker.fc-widget-content {
        display: none;
    } */
  </style>
@endsection

@section('body-class', 'col-md-8')

@section('inner-content')
  <div class="d-flex justify-content-between align-items-center pb-2 mt-3">
    <h3 class="mb-0">{{ $group->calendar_page }}</h3>
    @if($group->is_events_enabled && ($group->isUserAdmin($authUser->id) || $group->can_users_post_events))
    <a href="/groups/{{ $group->slug }}/events/new" class="btn btn-sm btn-secondary"><i class="icon-plus"></i> 
     @lang('events.New Event')</a>
    @endif
  </div>
  <div class="card">
    <div class="card-body">
      <div id='calendar'></div>
    </div>
  </div>
@endsection

@section('scripts')
  <script type="text/javascript" src="https://unpkg.com/@fullcalendar/core@4.2.0/main.min.js"></script>
  <script type="text/javascript" src="https://unpkg.com/@fullcalendar/daygrid@4.2.0/main.min.js"></script>
  <script type="text/javascript" src="https://unpkg.com/@fullcalendar/list@4.2.0/main.min.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
          plugins: [ 'dayGrid', 'list' ],
          defaultView: 'listMonth',
          events: [
            @foreach($group->all_events as $event)
            {
              title: `{!! $event->name !!}`,
              @if($event->end_date)end: "{{ $event->end_date->tz(request()->user()->timezone) }}",@endif
              url: "/groups/{{ $group->slug }}/events/{{ $event->id }}",

              @if(!$event->recur_every)
              start: "{{ $event->date->tz(request()->user()->timezone) }}",
              @else
                startTime: "{{ $event->date->tz(request()->user()->timezone)->toTimeString() }}",
                @if($event->end_date)endTime: "{{ $event->end_date->tz(request()->user()->timezone)->toTimeString() }}",@endif
                @if($event->recur_every == 'week')
                  startRecur: "{{ $event->date->tz(request()->user()->timezone) }}",
                  daysOfWeek: [{{ $event->date->tz($authUser->timezone)->dayOfWeek }}],
                @endif
                @if($event->recur_until)
                  endRecur: "{{ $event->recur_until->endOfDay()->toDateTimeString() }}"
                @endif
              @endif
            },
            @endforeach
          ],
          header: {
            left: 'prev,next today',
            center: 'title',
            right: 'listMonth,dayGridMonth'
          },
          listMonth: { 
            columnFormat: ['date', 'description'] 
          },
          views: {
            listMonth: { buttonText: "@lang('events.List View')" },
            dayGridMonth: { buttonText: "@lang('events.Calendar')" }
          },
        });

        calendar.render();

      });
      $( document ).ready(function() {
        $(".fc-list-item-marker").addClass("d-none");
        
        $('.fc-widget-header').attr('colspan',2);
    });
    </script>
@endsection
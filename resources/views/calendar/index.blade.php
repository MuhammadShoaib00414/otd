@extends('layouts.app')

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
    .fc-list-item-title {
      word-break: break-word;
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
  </style>
@endsection

@section('content')
<div style="background-color: #565d6b;">
  <div class="container-fluid py-3" style="position: relative;">
    <a href="{{ route('spa') }}" class="d-inline-block mb-2" style="font-size: 14px; color: #fff;"><i class="icon-chevron-small-left"></i> @lang('messages.my-dashboard')</a>
    <h4 class="mb-0 text-center no-underline" style="color: #fff;">@lang('events.Your Calendar')</h4>
  </div>
</div>
<div class="main-container bg-lightest-brand py-4">
  <div class="container-fluid">
    @if($events->where('is_live')->count())
      <div class="d-flex justify-content-around flex-wrap">
        @foreach($events->where('is_live')->unique('id')->sortByDesc('id') as $event)
          <div class="card card-body" style="max-width: 450px; min-width: 450px;">
            <b class="text-center">
              @if($event->is_live)
                @include('groups.events.live')
              @endif
              {{ $event->name }}</b>
            <span class="text-muted text-center">
              {{ $event->date->tz(request()->user()->timezone)->format('g:i a') }}
              @if($event->end_date)
                - {{ $event->end_date->tz(request()->user()->timezone)->format('g:i a') }}
              @endif
            </span>
            <a href="/groups/{{ $event->group->slug }}/events/{{ $event->id }}" class="btn btn-outline-primary mt-2">@lang('events.Event Details')</a>
          </div>
        @endforeach
      </div>
    @endif
    <div class="card">
      <div class="card-body">
        <div id='calendar'></div>
      </div>
    </div>
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
          locale: "{{ request()->user()->locale }}",
          plugins: [ 'list', 'dayGrid' ],
          defaultView: 'listMonth',
          events: [
            @foreach($events as $event)
              @if(isset($event->group))
              {
                title: `{!! $event->name !!}{!! $event->getUserLabel($authUser) !!}`,
                @if($event->end_date)end: "{{ $event->end_date->tz(request()->user()->timezone) }}",@endif
                @if(!$event->recur_every)
                start: "{{ $event->date->tz(request()->user()->timezone) }}",
                @else
                  startTime: "{{ $event->date->tz(request()->user()->timezone)->toTimeString() }}",
                  @if($event->end_date)endTime: "{{ $event->end_date->tz(request()->user()->timezone)->toTimeString() }}",@endif
                  @if($event->recur_every == 'week')
                    daysOfWeek: [{{ $event->date->tz($authUser->timezone)->dayOfWeek }}],
                  @endif
                  @if($event->recur_until)
                    endRecur: "{{ $event->recur_until->endOfDay()->toDateTimeString() }}",
                  @endif
                @endif
                @if($event->group->isUserMember(request()->user()->id))
                  url: "/groups/{{ $event->group->slug }}/events/{{ $event->id }}",
                @else
                  url: "/events/{{ $event->id }}",
                @endif
              },
              @endif
            @endforeach
          ],
          header: {
            left: 'prev,next',
            center: 'title',
            right: 'listMonth,dayGridMonth'
          },
          views: {
            listMonth: { buttonText: "@lang('events.List View')" },
            dayGridMonth: { buttonText: "@lang('events.Calendar')" }
          },
        });

        calendar.render();

      });
    </script>
@endsection
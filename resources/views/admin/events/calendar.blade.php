@extends('admin.events.layout')

@section('head')
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
  </style>
@endsection

@section('inner-page-content')    
    <div id='calendar'></div>
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
          events: [
            @foreach($events as $event)
            {
              title: "{!! $event->name !!}",
              start: "{{ $event->date->tz(request()->user()->timezone) }}",
              @if($event->end_date)end: "{{ $event->end_date->tz(request()->user()->timezone) }}",@endif
              url: "/admin/events/{{ $event->id }}"
            },
            @endforeach
          ],
          header: {
            left: 'prev,next today',
            center: 'title',
            right: 'listMonth,dayGridMonth'
          },
          views: {
            listMonth: { buttonText: 'List View' },
            dayGridMonth: { buttonText: 'Calendar' }
          },
        });

        calendar.render();

      });
    </script>
@endsection
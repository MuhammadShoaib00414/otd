@extends('groups.layout')

@section('stylesheets')
    @parent
    <style>
    .hover-hand:hover { cursor: pointer; }
    </style>
    <link rel="stylesheet" href="/revolvapp-2-3-2/css/revolvapp.min.css" />
@endsection

@section('body-class', 'col-md-9')

@section('inner-content')

  <a href="/groups/{{ $group->slug }}/sequence/reminders" class="d-inline-block mb-2 text-sm">
    <i class="icon-chevron-small-left"></i> <span>Back to reminders</span>
  </a>
  
  <div class="d-flex justify-content-between align-items-center mb-2">
    <h3 class="font-weight-bold mb-0">View Sequence Reminder</h3>
  </div>
  
    <form id="newreminderform" method="post" action="/groups/{{ $group->slug }}/sequence/reminders">
        @csrf
         <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <b>Email Subject: </b>{{ $reminder->subject }}
                        <div>
                            @if($reminder->is_enabled)
                                <span class="badge badge-primary mr-1">enabled</span>
                            @else
                                <span class="badge badge-light mr-1">disabled</span>
                            @endif
                            <span class="text-muted">Sends after {{ $reminder->send_after_days }} days</span>
                        </div>
                    </div>
                    <a href="/groups/{{ $group->slug }}/sequence/reminders/{{ $reminder->id }}/edit" class="btn btn-primary">Edit</a>
                </div>
            </div>
            <div class="form-group">
                <div id="emailbody"></div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script src="/revolvapp-2-3-2/revolvapp.min.js"></script>
    <script>
        emailEditor = Revolvapp('#emailbody', {
            editor: {
                path: '/revolvapp-1_0_7/',
                template: '/groups/{{ $group->slug }}/sequence/reminders/{{ $reminder->id }}/template',
                viewOnly: true,
            },
        });
    </script>
@endsection
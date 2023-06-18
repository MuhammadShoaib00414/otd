@extends('groups.layout')

@section('stylesheets')
@parent
  <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
  <link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">
  <style>
    .btn-grey {
        background-color: #dadcdf;
        border-color: #dadcdf;
        color: #645f5f;
    }
    .btn-grey:hover {
        background-color: #ced1d5;
        border-color: #ced1d5;
        color: #645f5f;
    }
    .hover-hand:hover { cursor: pointer; }
    .custom__tag {
      background-color: #f1f1f1;
      padding: 0.2em 0.5em;
      border-radius: 4px;
      margin-right: 0.25em;
    }
    .custom__remove {
      font-size: 20px;
      line-height: 3px;
      position: relative;
      top: 2px;
      padding-left: 0.1em;
    }
    .custom__remove:hover {
      cursor: pointer;
    }
    .multiselect__option--highlight {
      background: #ffc6be !important;
      color: #000;
    }
    .multiselect__option--highlight::after {
      background: #f19b8f !important;
      color: #000;
    }
    .nav-tabs .nav-item .nav-link:not(.active) {
      color: #515457;
    }
    .nav-item .nav-link.active {
      border-color: #1a2b40 !important;
      color: #1a2b40;
      font-weight: bold;
    }
  </style>
@endsection

@section('inner-content')
      <div class="card mt-3">
        <div class="card-body">
          <h5 class="card-title">@lang('events.New Event')</h5>

          @if($errors->any())
            <div class="alert alert-danger">
              <ul>
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form method="post" action="/groups/{{ $group->slug }}/events" enctype="multipart/form-data" id="eventform">
            @csrf
            @include('components.multi-language-text-input', ['label' => __('events.Event name'), 'name' => 'name', 'required' => true])
            <div class="form-row mb-3">
              <div class="col form-control-group">
                <label>@lang('events.Start Date')</label>
                <input onkeydown="event.preventDefault()" type="text" name="date" class="form-control" required value="{{ old('date') ?: \Carbon\Carbon::now()->format('m/d/y') }}" placeholder="mm/dd/yy" id="date">
              </div>
              <div class="col">
                <label>@lang('events.Start Time') <small class="text-muted">({{ request()->user()->timezone }})</small></label>
                <input autocomplete="off" type="text" name="time" class="form-control" required value="{{ old('time') ?: '10:00 am' }}" placeholder="hh:mm pm" id="time">
              </div>
            </div>
            <div class="form-row mb-3">
              <div class="col form-control-group">
                <label>@lang('events.End Date') <small class="text-muted">(@lang('general.optional'))</small></label>
                <input autocomplete="off" onkeydown="event.preventDefault()" type="text" name="end_date" class="form-control" placeholder="mm/dd/yy" id="end_date">
              </div>
              <div class="col">
                <label for="event_end_time">@lang('events.End Time')</label>
                <input autocomplete="off" type="text" name="event_end_time" class="form-control" required value="{{ old('end_time') ?: '10:30 am' }}" placeholder="hh:mm pm" id="end_time">
              </div>
            </div>
            <p class="text-danger d-none" id="date-warning">@lang('events.Warning! Your inputted end date is before the start date!')</p>
            <div class="form-row mb-3">
              <div class="col">
                <label for="max_participants">@lang('events.Max Participants') <small class="text-muted">(@lang('general.optional'))</small></label>
                <input autocomplete="off" type="text" name="max_participants" class="form-control">
              </div>
              <div class="col">
              </div>
            </div>
            <hr>
            <div class="form-group">
              <label>@lang('events.Event Image')</label>
              <input class="form-control-file form-control-lg d-inline-block" name="image" type="file" />
            </div>
            <div class="form-group">
              @include('components.multi-language-text-area', ['name' => 'description', 'label' => __('Event Description')])
            </div>
            @if(!$group->is_private && ($group->can_group_admins_invite_other_groups_to_events || request()->user()->is_admin))
              <div class="mb-3">
                 @include('groups.events.partials.invite')
              </div>
              <div class="form-row mb-3">
                <div class="col">
                  <label for="groups[]">@lang('events.Invite Groups') <small class="text-muted">  (@lang('general.optional'))</small></label>
                  @include('groups.events.partials.groupInvite', ['allGroups' => $groups, 'group' => $group, 'count' => 0])
                </div>
              </div>
            @endif
            
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="allow_rsvps" id="defaultCheck1" checked>
              <label class="form-check-label" for="defaultCheck1" style="font-size: 1em;">
                @lang('events.Enable Online RSVPs')
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="post_to_group_feed" id="post_to_group_feed" checked>
              <label class="form-check-label" for="post_to_group_feed" style="font-size: 1em;">
                @lang('events.Post to Group Feed')
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="recur_weekly" id="recur_weekly">
              <label class="form-check-label" for="recur_weekly" style="font-size: 1em;">
                @lang('events.Recur weekly')
              </label>
            </div>
            <div class="d-none" id="recurrance_end_date_container">
              <div class="form-group w-50">
                <label for="recurrance_end_date">@lang('events.Recurrance end date') <small class="text-muted">(@lang('general.optional'))</small></label>
                <input autocomplete="off" onkeydown="event.preventDefault()" type="text" name="recurrance_end_date" class="form-control" placeholder="mm/dd/yy" id="recurrance_end_date">
              </div>
            </div>  
            @if(is_zoom_enabled())
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="enable_zoom" id="enable_zoom">
              <label class="form-check-label" for="enable_zoom" style="font-size: 1em;">
                @lang('events.Allow Zoom Meeting')
              </label>
            </div>
            <div class="d-none" id="zoom_details_container">
              <div class="form-group">
                <label for="zoom_meeting_link">Zoom Meeting Invite Link</label>
                <input type="text" name="zoom_meeting_link" id="zoom_meeting_link" class="form-control zoom_details">
                <small class="text-muted">Host must join through zoom application.</small>
              </div>
            </div>
            @endif
            <div class="mt-3 mb-1" id="linksContainer">
              <div id="links">
              </div>
              <button id="addLinkButton" class="btn btn-secondary btn-sm mb-3">@lang('events.Add link')</button>
            </div>
            <div class="text-left">
              <button id="submitButton" type="submit" class="btn btn-secondary">{{ $group->calendar_page != "Calendar" ? __('events.Post New') . ' ' . $group->calendar_page : __('events.Post New Event') }}</button>
            </div>
          </form>
        </div>
      </div>
      <div class="d-none">
        <div id="emptyLinkGroup" class="form-group">
          <div class="form-row">
            <div class="col-2">
              <label class="mr-1 mt-4" for="links[][title]" id="newLinkTitleLabel"><b>@lang('events.Title')</b></label>
            </div>
            <div class="{{ getsetting('is_localization_enabled') ? 'col-5' : 'mt-auto' }}">
              @if(getsetting('is_localization_enabled'))
              <p>@lang('messages.english')</p>
              @endif
              <input maxlength="50" class="form-control" type="text" id="newLinkTitle">
            </div>
            @if(getsetting('is_localization_enabled'))
            <div class="col-5">
              <p>@lang('messages.spanish')</p>
              <input maxlength="50" class="form-control" type="text" id="newLinkTitleEs">
            </div>
            @endif
          </div>
          <label for="links[][url]" id="newLinkUrlLabel"><b>@lang('general.url')</b></label>
          <input class="w-50 form-control" type="url" id="newLinkUrl">
          <a href="#" class="text-small removeLinkButton">@lang('general.remove')</a>
          <hr class="my-1">
        </div>
        <hr class="my-2">
      </div>
@endsection

@section('scripts')
  <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
  <script src="https://unpkg.com/vue-multiselect@2.1.0"></script>
  <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
  <script>
  Vue.component('multiselect', window.VueMultiselect.default);

      var app = new Vue({
        el: '#eventform',
        data: {
          selected: [],
          options: [
            @foreach(request()->user()->visible_platform_users as $user)
                {
                    name: '{{ $user->name }}',
                    id: '{{ $user->id }}',
                },
            @endforeach
           ],
          isLoading: false,
          timeout: null,
        },
        methods: {
          clearAll: function () {
            this.selected = [];
          },
          saveUsers: function () {
            $.each(this.selected, function(index, user) {
              $('<input>').attr({
                type: 'hidden',
                name: 'users[]',
                value: user.id
              }).appendTo('form');
            });
          },
        }
      });

      $('#linksButton').click(function(e) {
      e.preventDefault();
    });

  $('#enable_zoom').change(function() {
    if($(this).is(':checked'))
    {
      $('#zoom_meeting_link').prop('required', true);
      $('#zoom_details_container').removeClass('d-none');
    }
    else
    {
      $('#zoom_meeting_link').prop('required', false);
      $('#zoom_details_container').addClass('d-none');
    }
  });

  $('#addLinkButton').on('click', function (e) {
      e.preventDefault();
      var newLinkGroup = $('#emptyLinkGroup').clone().appendTo('#links');
      var numberOfLinks = $('#links').children().length;
      newLinkGroup.attr('id', '');
      newLinkGroup.find('#newLinkTitle').attr('name', 'links['+numberOfLinks+'][title]').attr('id', 'links['+numberOfLinks+'][title]');
      newLinkGroup.find('#newLinkTitleEs').attr('name', 'localization[es][links]['+(numberOfLinks - 1)+'][title]').attr('id', 'localization[es][links]['+numberOfLinks+'][title]');
      newLinkGroup.find('#newLinkTitleLabel').attr('for', 'links['+numberOfLinks+'][title]').attr('id', '');
      newLinkGroup.find('#newLinkUrl').attr('name', 'links['+numberOfLinks+'][url]').attr('id', 'links['+numberOfLinks+'][url]');;
      newLinkGroup.find('#newLinkUrlLabel').attr('for', 'links['+numberOfLinks+'][url]').attr('id', '');
    });

    $('#eventform').on('submit', function(e) {
      app.saveUsers();
      $('#submitButton').prop('disabled', true);
      $('#eventform').submit();
    });

    $('#linksButton').click(function(e) {
      e.preventDefault();
    });

    $(document).ready(function () {
      $('#date').datepicker({
      uiLibrary: 'bootstrap4',
      format: 'mm/dd/yy'
    }).change(evt => {
      var selectedDate = $('#date').val();
      var now = new Date();
      now.setHours(0,0,0,0);
      if (Date.parse(selectedDate) < Date.parse(now)) {
        $('#date-warning').removeClass('d-none');
      } else {
        $('#date-warning').addClass('d-none');
      }
    });
    });

    $('#recur_weekly').change(function() {
      if($(this).is(':checked'))
        $('#recurrance_end_date_container').removeClass('d-none');
      else
        $('#recurrance_end_date_container').addClass('d-none');
    });

    Date.prototype.addDays = function(days) {
      var date = new Date(this.valueOf());
      date.setDate(date.getDate() + days);
      return date;
    }

    $('#recurrance_end_date').datepicker({
      uiLibrary: 'bootstrap4',
      format: 'mm/dd/yy',
      //6 days to be changed when we add different recur periods
      minDate: new Date().addDays(6),
    });

    $('#end_date').datepicker({
      uiLibrary: 'bootstrap4',
      format: 'mm/dd/yy'
    }).change(evt => {
      var end = $('#end_date').val();
      var start = $('#date').val();
      if (Date.parse(end) < Date.parse(start)) {
         $('#date-warning').removeClass('d-none');
      } else {
        $('#date-warning').addClass('d-none');
      }
    });

    $('#time').timepicker({
        timeFormat: 'h:mm p',
        dropdown: true,
    });

    $('#end_time').timepicker({
        timeFormat: 'h:mm p',
        dropdown: true,
    });

    $('#linksContainer').on('click', '.removeLinkButton', function (e) {
      e.preventDefault();
      $(this).parent().remove();
    });

    $('#inviteUserButton').click(function(e){
      e.preventDefault();
    });

  </script>
  <link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">
@endsection
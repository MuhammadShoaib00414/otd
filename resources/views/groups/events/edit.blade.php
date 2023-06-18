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
          <h5 class="card-title">Edit Event</h5>
          @if($errors->any())
            <div class="alert alert-danger">
              <ul>
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif
          
          <form id="editForm" method="post" action="/groups/{{ $group->slug }}/events/{{ $event->id }}" enctype="multipart/form-data">
            @csrf
            @method('put')
            @include('components.multi-language-text-input', ['label' => __('events.Event name'), 'name' => 'name', 'required' => true, 'value' => $event->name_raw, 'localization' => $event->localization])
            <div class="form-row mb-3">
              <div class="col">
                <label>@lang('events.Start Date')</label>
                <input onkeydown="event.preventDefault()" type="text" name="event_date" class="form-control" required value="{{ $event->date->tz(request()->user()->timezone)->format('m/d/y') }}" placeholder="mm/dd/yy" id="date">
              </div>
              <div class="col">
                <label>@lang('events.Start Time') <small class="text-muted">({{ request()->user()->timezone }})</small></label>
                <input autocomplete="off" type="text" name="event_time" class="form-control" required value="{{ $event->date->tz(request()->user()->timezone)->format('g:i a') }}" placeholder="hh:mm pm" id="time">
              </div>
            </div>
            <div class="form-row mb-3">
              <div class="col form-control-group">
                <label>@lang('events.End Date') <small class="text-muted">(@lang('general.optional'))</small></label>
                <input onkeydown="event.preventDefault()" type="text" name="end_date" class="form-control" value="{{ $event->end_date->tz(request()->user()->timezone)->format('m/d/y') }}" placeholder="mm/dd/yy" id="end_date">
              </div>
              <div class="col">
                <label for="event_end_time">@lang('events.End Time')</label>
                <input autocomplete="off" type="text" name="event_end_time" class="form-control" required value="{{ ($event->end_date) ? $event->end_date->tz(request()->user()->timezone)->format('g:i a') : '' }}" placeholder="hh:mm pm" id="end_time">
              </div>
            </div>
            <div class="form-row mb-3">
              <div class="col">
                <label for="max_participants">@lang('events.Max Participants') <small class="text-muted">(@lang('general.optional'))</small></label>
                <input autocomplete="off" type="text" name="max_participants" class="form-control form-control-sm" value="{{ $event->max_participants ? $event->max_participants : '' }}">
              </div>
            </div>
            @if(!$group->is_private && $group->can_group_admins_invite_other_groups_to_events)
              <div class="mb-3">
                @include('groups.events.partials.invite')
              </div>

              <div class="form-row mb-3">
                <div class="col">
                  <label for="groups[]">@lang('events.Invite Groups') <small class="text-muted">  (@lang('general.optional'))</small></label>
                  @foreach($groups->where('is_events_enabled',1) as $groupToInvite)
                    @if($group->id != $groupToInvite->id)
                      <div class="form-check pl-0 mb-1">
                        <input type="checkbox" class="custom-checkbox" name="groups[]" value="{{ $groupToInvite->id }}" {{ $event->isGroupInvited($groupToInvite->id) ? 'checked' : '' }}>
                        <label class="form-check-label" for="groups[]">{{ $groupToInvite->name }}</label>
                      </div>
                    @else
                      <div class="form-check pl-0 mb-1">
                        <input disabled type="checkbox" class="custom-checkbox" name="groups[]" value="{{ $groupToInvite->id }}" checked>
                        <label class="form-check-label" for="groups[]">{{ $groupToInvite->name }}</label>
                      </div>
                    @endif
                    @foreach($groupToInvite->subgroups->where('is_events_enabled',1) as $subgroupToInvite)
                      @if($group->id != $subgroupToInvite->id)
                        <div class="form-check pl-0 ml-3 mb-1">
                          <input type="checkbox" class="custom-checkbox" name="groups[]" value="{{ $subgroupToInvite->id }}" {{ $event->isGroupInvited($subgroupToInvite->id) ? 'checked' : '' }}>
                          <label class="form-check-label" for="groups[]">{{ $subgroupToInvite->name }}</label>
                        </div>
                      @else
                        <div class="form-check pl-0 ml-3 mb-1">
                          <input disabled type="checkbox" class="custom-checkbox" name="groups[]" value="{{ $subgroupToInvite->id }}" checked>
                          <label class="form-check-label" for="groups[]">{{ $subgroupToInvite->name }}</label>
                        </div>
                      @endif
                    @endforeach
                  @endforeach
                </div>
                <div class="col">
                </div>
              </div>
            @endif
            <hr>
            <div class="form-group">
              <label>@lang('events.Event Image')</label>
              @if($event->image)
                <p>@lang('events.Current image')</p>
                <img src="{{ $event->image_path }}" style="width: 100%; max-width: 250px;">
              @endif
              <p class="mt-3 mb-0">@lang('events.Upload a new photo to change image:')</p>
              <input class="form-control-file form-control-lg d-inline-block" name="image" type="file" />
            </div>
            @include('components.multi-language-text-area', ['name' => 'description', 'label' => __('Event Description'), 'value' => $event->getOriginal('description'), 'localization' => $event->localization])
            <div>

            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="allow_rsvps" id="defaultCheck1" {{ ($event->allow_rsvps) ? 'checked' : '' }}>
              <label class="form-check-label" for="defaultCheck1" style="font-size: 1em;">
                @lang('events.Enable Online RSVPs')
              </label>
            </div>

            <div class="form-check">
              <input value="1" class="form-check-input" type="checkbox" name="post_to_group_feed" id="post_to_group_feed" {{ $event->listing->is_enabled ? 'checked' : '' }}>
              <label class="form-check-label" for="post_to_group_feed" style="font-size: 1em;">
                @lang('events.Post to Group Feed')
              </label>
            </div>

            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="recur_weekly" id="recur_weekly" {{ $event->recur_every ? 'checked' : '' }}>
              <label class="form-check-label" for="recur_weekly" style="font-size: 1em;">
                @lang('events.Recur weekly')
              </label>
            </div>
            <div class="{{ $event->recur_every ? '' : 'd-none' }}" id="recurrance_end_date_container">
              <div class="form-group w-50">
                <label for="recurrance_end_date">@lang('events.Recurrance end date') <small class="text-muted">(@lang('general.optional'))</small></label>
                <input autocomplete="off" onkeydown="event.preventDefault()" type="text" name="recurrance_end_date" class="form-control" placeholder="mm/dd/yy" id="recurrance_end_date" value="{{ $event->recur_until ? $event->recur_until->format('m/d/y') : '' }}">
              </div>
            </div>  

            @if(is_zoom_enabled())
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="enable_zoom" id="enable_zoom" {{ $event->zoom_meeting_id ? 'checked' : '' }}>
                <label class="form-check-label" for="enable_zoom" style="font-size: 1em;">
                  @lang('events.Allow Zoom Meeting')
                </label>
              </div>
              <div class="d-none" id="zoom_details_container">
                <div class="form-group">
                  <label for="zoom_meeting_link">Zoom Meeting Invite Link</label>
                  <input type="text" name="zoom_meeting_link" id="zoom_meeting_link" class="form-control zoom_details" value="{{ $event->zoom_invite_link }}">
                </div>
              </div>
            @endif

            <div class="my-3" id="linksContainer">
              <div id="links">
                @if($event->custom_menu)
                @foreach($event->custom_menu as $index => $link)
                <div class="form-group">
                  <div class="form-row">
                    <div class="col-2">
                      <label for="links[{{ $index }}][title]" id="newLinkTitleLabel"><b>@lang('events.Title')</b></label>
                    </div>
                    <div class="{{ getsetting('is_localization_enabled') ? 'col-5' : 'mt-auto' }}">
                      @if(getsetting('is_localization_enabled'))
                      <p>@lang('messages.english')</p>
                      @endif
                      <input maxlength="50" class="form-control" type="text" value="{{ $link['title'] }}" id="links[{{ $index }}][title]" name="links[{{ $index }}][title]">
                    </div>
                    @if(getsetting('is_localization_enabled'))
                    <div class="col-5">
                      <p>@lang('messages.spanish')</p>
                      <input maxlength="50" class="form-control" type="text" value="{{ $event->localizedLinkTitle($index, 'es') }}" id="localization[es][links][{{ $index }}][title]" name="localization[es][links][{{ $index }}][title]">
                    </div>
                    @endif
                  </div>
                  <label for="links[{{ $index }}][url]" id="newLinkUrlLabel"><b>@lang('general.url')</b></label>
                  <input class="w-50 form-control" type="url" value="{{ $link['url'] }}" id="links[{{ $index }}][url]" name="links[{{ $index }}][url]">
                  <a href="#" class="text-small removeLinkButton">@lang('general.remove')</a>
                  <hr class="my-2">
                </div>
                @endforeach
                @endif
                <div class="form-check"><input type="checkbox" name="update_posted_date" value="1" id="update_posted_date" class="form-check-input"> <label for="update_posted_date" class="form-check-label" style="font-size: 1em;">
                Update posted date             
                 </label></div>
              </div>
              <button id="addLinkButton" class="btn btn-secondary btn-sm mb-3">@lang('events.Add link')</button>
            </div>

            <div class="text-left">
              <button id="submitButton" type="button" class="btn btn-secondary">@lang('general.save_changes')</button>
            </div>
          </form>
        </div>
      </div>
      <div class="d-none">
        <div id="emptyLinkGroup" class="form-group">
          <div class="form-row">
            <div class="col">
              <label class="mr-1 mt-4" for="links[][title]" id="newLinkTitleLabel"><b>@lang('events.Title')</b></label>
            </div>
            <div class="col-5">
              <p>@lang('messages.english')</p>
              <input maxlength="50" class="form-control" type="text" id="newLinkTitle">
            </div>
            <div class="col-5">
              <p>@lang('messages.spanish')</p>
              <input maxlength="50" class="form-control" type="text" id="newLinkTitleEs">
            </div>
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
  <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
  <script src="https://unpkg.com/vue-multiselect@2.1.0"></script>
  <script>
    Vue.component('multiselect', window.VueMultiselect.default);

      var app = new Vue({
        el: '#editForm',
        data: {
          selected: [
            @foreach($event->invited_users as $user)
              {
                name: '{{ $user->name }}',
                id: '{{ $user->id }}',
              },
            @endforeach
          ],
          options: [
            @foreach(request()->user()->visible_platform_users->whereNotIn('id', $event->invited_user_ids) as $user)
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
    $('#submitButton').click(function(e) {
      app.saveUsers();
      $('#editForm').submit();
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

    window.onload = function() {
      console.log('hah');
      if($('#enable_zoom').is(':checked'))
      {
        $('#zoom_meeting_link').prop('required', true);
        $('#zoom_details_container').removeClass('d-none');
      }
      else
      {
        $('#zoom_meeting_link').prop('required', false);
        $('#zoom_details_container').addClass('d-none');
      }
    };

    $('#linksButton').click(function(e) {
      e.preventDefault();
    });
    $('#date').datepicker({
      uiLibrary: 'bootstrap4',
      format: 'mm/dd/yy'
    });
    $('#time').timepicker({
        timeFormat: 'h:mm p',
        dropdown: true,
    });
    $('#end_date').datepicker({
      uiLibrary: 'bootstrap4',
      format: 'mm/dd/yy'
    });
    $('#end_time').timepicker({
        timeFormat: 'h:mm p',
        dropdown: true,
    });

    $('#addLinkButton').on('click', function (e) {
      e.preventDefault();
      var newLinkGroup = $('#emptyLinkGroup').clone().appendTo('#links');
      var numberOfLinks = $('#links').children().length;
      newLinkGroup.attr('id', '');
      newLinkGroup.find('#newLinkTitle').attr('name', 'links['+numberOfLinks+'][title]').attr('id', 'links['+numberOfLinks+'][title]');
      newLinkGroup.find('#newLinkTitleLabel').attr('for', 'links['+numberOfLinks+'][title]').attr('id', '');
      newLinkGroup.find('#newLinkUrl').attr('name', 'links['+numberOfLinks+'][url]').attr('id', 'links['+numberOfLinks+'][url]');;
      newLinkGroup.find('#newLinkUrlLabel').attr('for', 'links['+numberOfLinks+'][url]').attr('id', '');
    });

    $('#linksContainer').on('click', '.removeLinkButton', function (e) {
      e.preventDefault();
      $(this).parent().remove();
    })
  </script>
@endsection
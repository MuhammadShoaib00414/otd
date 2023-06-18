@extends('admin.layout')

@section('head')
    @parent
  <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
@endsection

@section('page-content')
    <div class="d-flex justify-content-between align-items-center">
        <h5 class="m-0">{{ $event->name }}</h5>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-6">
            <form method="post" action="/admin/events/{{ $event->id }}" enctype="multipart/form-data">
                @csrf
                @method('put')
                @include('components.multi-language-text-input', ['label' => 'Event name', 'name' => 'name', 'required' => 'true', 'value' => $event->name, 'localization' => $event->localization])
                <div class="form-row mb-3">
                  <div class="col">
                    <label>Date</label>
                    <input type="text" name="date" class="form-control" required value="{{ $event->date->format('m/d/y') }}" placeholder="mm/dd/yy" id="date">
                  </div>
                  <div class="col">
                    <label>Time <small class="text-muted">({{ request()->user()->timezone}})</small></label>
                    <input type="text" name="time" class="form-control" required value="{{ $event->date->tz(request()->user()->timezone)->format('g:i a') }}" placeholder="hh:mm pm" id="time">
                  </div>
                </div>
                <div class="form-row mb-3">
                  <div class="col">
                  </div>
                  <div class="col">
                    <label for="event_end_time">End Time <small class="text-muted">({{ request()->user()->timezone}})</small></label>
                    <input type="text" name="event_end_time" class="form-control" required value="{{ ($event->end_date) ? $event->end_date->tz(request()->user()->timezone)->format('g:i a') : '' }}" placeholder="hh:mm pm" id="end_time">
                  </div>
                </div>
                <hr>
                <div class="form-group">
                  <label>Event Image</label>
                  @if($event->image)
                    <p>Current image</p>
                    <img src="{{ $event->image_path }}" style="width: 100%; max-width: 250px;">
                  @endif
                  <p class="mt-3 mb-0">Upload a new photo to change image:</p>
                  <input class="form-control-file d-inline-block my-2" name="image" type="file" />
                </div>
                @include('components.multi-language-text-area', ['label' => 'Event description', 'name' => 'description', 'value' => $event->description, 'localization' => $event->localization])
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="allow_rsvps" id="defaultCheck1" {{ ($event->allow_rsvps) ? 'checked' : '' }}>
                  <label class="form-check-label" for="defaultCheck1" style="font-size: 1em;">
                    Enable Online RSVPs
                  </label>
                </div>

                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" name="recur_weekly" id="recur_weekly" {{ $event->recur_every ? 'checked' : '' }}>
                  <label class="form-check-label" for="recur_weekly" style="font-size: 1em;">
                    @lang('events.Recur weekly')
                  </label>
                </div>
                <div class="{{ $event->recur_every ? '' : 'd-none' }} mb-2" id="recurrance_end_date_container">
                  <div class="form-group w-50">
                    <label for="recurrance_end_date">@lang('events.Recurrance end date') <small class="text-muted">(@lang('general.optional'))</small></label>
                    <input autocomplete="off" onkeydown="event.preventDefault()" type="text" name="recurrance_end_date" class="form-control" placeholder="mm/dd/yy" id="recurrance_end_date" value="{{ $event->recur_until ? $event->recur_until->format('m/d/y') : '' }}">
                  </div>
                </div> 

                <div class="form-row my-3">
                  <div class="col">
                    <label for="groups[]">@lang('events.Invite Groups')</label>
                    @include('admin.events.partials.groupInvite', ['allGroups' => $groups, 'count' => 0, 'event' => $event, 'disabledGroupId' => $event->group_id])
                  </div>
                </div>

                <div class="text-left mt-3">
                  <button type="submit" class="btn btn-primary">@lang('general.save') changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
  <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
  <script>
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


    $('#date').datepicker({
      uiLibrary: 'bootstrap4',
      format: 'mm/dd/yy'
    });
    $('#time').timepicker({
        timeFormat: 'h:mm p',
        dropdown: false,
    });
  </script>
@endsection
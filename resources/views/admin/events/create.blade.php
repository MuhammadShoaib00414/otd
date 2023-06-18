@extends('admin.layout')

@section('head')
    @parent
  <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
  <link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">
  <style>
  .custom__tag {
      background-color: #1c2c40;
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
      background: #29405d !important;
      color: #fff;
    }
    .multiselect__option--highlight::after {
      background: #1c2c40 !important;
      color: #fff;
    }
</style>
@endsection

@section('page-content')
    <div class="d-flex justify-content-between align-items-center">
        <h5 class="m-0">New Event</h5>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-6">
            <form id="form" method="post" action="/admin/events/create" enctype="multipart/form-data">
                @csrf
                @include('components.multi-language-text-input', ['label' => 'Event name', 'name' => 'name', 'required' => 'true'])
                <div class="form-group">
                  <label for="group">Group</label>
                  <select name="group" class="custom-select">
                    @foreach($groups as $group)
                      <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-row mb-3">
                  <div class="col">
                    <label>Date</label>
                    <input type="text" name="date" class="form-control" required placeholder="mm/dd/yy" id="date" value="{{ old('date') ?: \Carbon\Carbon::now()->format('m/d/y') }}">
                  </div>
                  <div class="col">
                    <label>Time</label>
                    <input type="text" name="time" class="form-control" required placeholder="hh:mm pm" id="time">
                  </div>
                </div>
                <div class="form-row mb-3">
                  <div class="col">
                  </div>
                  <div class="col">
                    <label for="event_end_time">End Time</label>
                    <input type="text" name="event_end_time" class="form-control" required placeholder="hh:mm pm" id="end_time">
                  </div>
                </div>
                <hr>
                <div class="form-group">
                  <label>Event Image</label>
                  <p class="mt-3 mb-0">Upload a new photo to change image:</p>
                  <input class="form-control-file d-inline-block my-2" name="image" type="file" />
                </div>
                @include('components.multi-language-text-area', ['label' => 'Event description', 'name' => 'description'])
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" name="allow_rsvps" id="defaultCheck1">
                  <label class="form-check-label" for="defaultCheck1" style="font-size: 1em;">
                    Enable Online RSVPs
                  </label>
                </div>

                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" name="recur_weekly" id="recur_weekly">
                  <label class="form-check-label" for="recur_weekly" style="font-size: 1em;">
                    @lang('events.Recur weekly')
                  </label>
                </div>
                <div class="d-none mb-2" id="recurrance_end_date_container">
                  <div class="form-group w-50">
                    <label for="recurrance_end_date">@lang('events.Recurrance end date') <small class="text-muted">(@lang('general.optional'))</small></label>
                    <input autocomplete="off" onkeydown="event.preventDefault()" type="text" name="recurrance_end_date" class="form-control" placeholder="mm/dd/yy" id="recurrance_end_date">
                  </div>
                </div> 

                <div class="mb-3">
                  @include('groups.events.partials.invite')
                </div>
                <div class="form-row mb-3">
                  <div class="col">
                    <label for="groups[]">@lang('events.Invite Groups') <small class="text-muted">  (@lang('general.optional'))</small></label>
                    @include('admin.events.partials.groupInvite', ['allGroups' => $groups, 'count' => 0])
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
  <script src="https://unpkg.com/vue-multiselect@2.1.0"></script>
  <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
  <script>
    Vue.component('multiselect', window.VueMultiselect.default);

     var app = new Vue({
        el: '#form',
        data: {
          selected: [],
          options: [
            @foreach(\App\User::all() as $user)
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

    $('#date').datepicker({
      uiLibrary: 'bootstrap4',
      format: 'mm/dd/yy'
    });
    $('#time').timepicker({
        timeFormat: 'h:mm p',
        dropdown: false,
    });
    $('#recurrance_end_date').datepicker({
      uiLibrary: 'bootstrap4',
      format: 'mm/dd/yy',
      //6 days to be changed when we add different recur periods
      minDate: new Date().addDays(6),
    });
  </script>
@endsection
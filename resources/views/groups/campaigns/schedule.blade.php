@extends('groups.layout')


@section('stylesheets')
    @parent
    <link rel="stylesheet" href="/revolvapp-1_0_7/css/revolvapp.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
    <style>
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
          background: #ffc6be;
          color: #000;
        }
        .multiselect__option--highlight::after {
          background: #f19b8f;
          color: #000;
        }
    </style>
@endsection

@section('body-class', 'col-md-9')

@section('inner-content')
    <div class="d-flex mb-3mt-3">
        <h4 class="mb-0">@lang('campaigns.Send'): {{ $campaign->email_subject }}</h4>
    </div>
    @if ($errors->any())
          <div class="alert alert-danger">
              @foreach ($errors->all() as $error)
                  <div>{{ $error }}</div>
              @endforeach
          </div>
    @endif
    <form method="post" action="/groups/{{ $group->slug }}/email-campaigns/{{ $campaign->id }}/review">
        @csrf
        @method('put')

        <div class="card mt-3" style="max-width: 700px;">
          <div class="card-body">
            <p><b>@lang('campaigns.Schedule To Send On')</b></p>
            <div class="form-row mb-3">
              <div class="col">
                <label>@lang('general.date')</label>
                <input type="text" name="date" class="form-control" required value="{{ \Carbon\Carbon::now()->format('m/d/y') }}" placeholder="mm/dd/yy" id="date">
              </div>
              <div class="col">
                <label>@lang('general.time') <small class="text-muted">({{ request()->user()->timezone}})</small></label>
                <input type="text" name="time" class="form-control" required value="{{ \Carbon\Carbon::now()->tz(request()->user()->timezone)->format('g:i a') }}" placeholder="hh:mm pm" id="time">
              </div>
            </div>
          </div>
        </div>

        <div class="card" id="app" style="max-width: 700px;">
            <div class="card-body">
                <p><b>@lang('campaigns.Who Should Receive this Campaign?')</b></p>
                <div class="form-group">
                    <label>@lang('campaigns.Send to groups')</label>
                    <select class="custom-select" name="groups[]" multiple style="height: 10em;" required>
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @foreach($group->subgroups as $subgroup)
                        <option value="{{ $subgroup->id }}">Subgroup: {{ $subgroup->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>@lang('campaigns.Send to users')</label>
                    <multiselect v-model="selected" id="ajax" label="name" track-by="id" placeholder="Type to search" open-direction="bottom" :options="options" :multiple="true" :searchable="true" :loading="isLoading" :internal-search="false" :clear-on-select="true" :close-on-select="true" :options-limit="300" :limit="3" :max-height="600" :show-no-results="true" :hide-selected="true" @search-change="asyncFind">
                      <template slot="tag" slot-scope="{ option, remove }"><span class="custom__tag"><span>@{{ option.name }}</span><span class="custom__remove" @click="remove(option)">&times;</span></span></template>
                      <template slot="clear" slot-scope="props">
                        <div class="multiselect__clear" v-if="selected.length" @mousedown.prevent.stop="clearAll(props.search)"></div>
                      </template><span slot="noResult">@lang('campaigns.No users found. Consider changing the search query.')</span>
                    </multiselect>
                </div>
                <hr>
                <div class="mt-3 text-right">
                    <button type="submit" @click.prevent="submitForm()" class="btn btn-primary">@lang('campaigns.Review') <i class="fas fa-angle-right ml-1"></i></button>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script src="https://unpkg.com/vue-multiselect@2.1.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
    <script>
      $('#date').datepicker({
        uiLibrary: 'bootstrap4',
        format: 'mm/dd/yy'
      });
      $('#time').timepicker({
          timeFormat: 'h:mm p',
          dropdown: false,
      });

      Vue.component('multiselect', window.VueMultiselect.default);

      var app = new Vue({
        el: '#app',
        data: {
          selected: [],
          options: [],
          isLoading: false,
          timeout: null,
        },
        methods: {
          asyncFind: function (query) {
            var vthis = this;
            clearTimeout(this.timeout);
            this.timeout = setTimeout(function () {
              this.isLoading = true;
              $.ajax({
                url: '/api/search/',
                data: { q: query },
                success: function (response) {
                  vthis.isLoading = false;
                  vthis.options = response;
                }
              });
            }, 100);
          },
          clearAll: function () {
            this.selected = [];
          },
          submitForm: function () {
            var vthis = this;
            $.each(this.selected, function(index, recipient) {
              $('<input>').attr({
                type: 'hidden',
                name: 'users[]',
                value: recipient.id
              }).appendTo('form');
            });

            $('form').submit();
          }
        }
      })
    </script>
  @endsection
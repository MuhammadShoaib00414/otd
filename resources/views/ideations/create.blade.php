@extends('ideations.layout')

@section('stylesheets')
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
      background: #ffc6be;
      color: #000;
    }
    .multiselect__option--highlight::after {
      background: #f19b8f;
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
    <div class="d-flex my-3 justify-content-between align-items-center">
        <h4 class="mb-0">@lang('ideations.new')</h4>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger">
          <ul>
              @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
              @endforeach
          </ul>
        </div>
    @endif
    <div class="card">
        <div class="card-body">
            <form action="/ideations" method="post">
                @csrf
                <div class="form-group">
                    <label for="title">@lang('ideations.title-prompt')</label>
                    <input type="text" name="name" dusk="ideation-name" class="form-control" value="{{ old('name') }}" required maxlength="75">
                </div>
                <div class="form-group">
                    <label for="title">@lang('general.details')</label>
                    <textarea class="form-control" required rows="4" dusk="ideation-body" value="{{ old('body') }}" name="body"></textarea>
                </div>
                <hr>
                <div class="form-group mb-4">
                    <label for="max_participants">@lang('ideations.limit-participants')</label>
                    <input type="text" name="max_participants" id="max_participants" dusk="max-participants" class="form-control" style="max-width: 80px;">
                    <span class="small text-muted">@lang('ideations.limit-participants-description')</span>
                </div>
                <div class="form-group">
                    <label for="title">@lang('ideations.groups-prompt')</label>
                    @foreach($groups as $group)
                      <div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" value="{{ $group->id }}" name="groups[]" id="group{{ $group->id }}">
                        <label class="form-check-label" for="group{{ $group->id }}" style="font-size: 16px;">
                          {{ $group->name }}
                        </label>
                      </div>
                      @foreach($group->subgroups as $subgroup)
                        <div class="form-check mb-1 ml-3">
                          <input class="form-check-input" type="checkbox" value="{{ $subgroup->id }}" name="groups[]" id="group{{ $subgroup->id }}">
                          <label class="form-check-label" for="group{{ $subgroup->id }}" style="font-size: 16px;">
                            {{ $subgroup->name }}
                          </label>
                        </div>
                      @endforeach
                    @endforeach
                </div>
                <div>
                    @include('ideations.partials.inviteOnCreate')
                </div>
                <div class="text-right">
                    <button type="submit" dusk="submit" class="btn btn-secondary">@lang('ideations.save-and-post')</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://unpkg.com/vue-multiselect@2.1.0"></script>
  <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script>
    $('#inviteUserButton').click(function (e) {
        e.preventDefault();
    });

    Vue.component('multiselect', window.VueMultiselect.default);

    var app = new Vue({
    el: '#inviteUserModal',
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
      sendInvite: function () {
        var vthis = this;
        $.each(this.selected, function(index, recipient) {
          $('<input>').attr({
            type: 'hidden',
            name: 'invite[]',
            value: recipient.id
          }).appendTo('form');
        });
      }
    }
    });
    $('.nonSubmitButton').click(function (e) {
        e.preventDefault();
    });
    </script>
@endsection
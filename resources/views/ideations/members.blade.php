@extends('ideations.show.layout')

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
    .hover-hand:hover { cursor: pointer; }
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
<div class="text-right mb-2">
  <div class="d-flex flex-row-reverse">
    <div style="max-width: 300px;">
      @include('ideations.partials.invite', ['ideation' => $ideation])
    </div>
  </div>
</div>
<div class="row">
    <div class="col-md-12">
      <div class="row justify-content-center align-items-stretch">
        @foreach($ideation->participants as $user)
          <a href="/users/{{ $user->id }}" class="card col-sm-6 mx-1 mb-2 px-3 no-underline" style="flex: 1; min-width: 250px; position: relative;">
              <div class="card-body d-flex align-items-center justify-content-center">
                <div class="d-flex flex-column align-items-center justify-content-center">
                  <div class="mb-2" style="height: 5.5em; width: 5.5em; border-radius: 50%; background-image: url('{{ $user->photo_path }}'); background-size: cover; background-position: center;">
                  </div>
                  <div class="pt-1 text-center">
                    <span class="d-block" style="color: #343a40; font-weight: 600;">{{ $user->name }}</span>
                    <span class="d-block card-subtitle my-1 text-muted">{{ $user->job_title }}</span>
                    <span class="d-block mt-1 text-muted">{{ $user->company }}</span>
                  </div>
                </div>
              </div>
              @if(request()->user()->is_admin || $ideation->is_current_user_participant)
              <div class="d-flex justify-content-end mt-3 pt-2" style="position: absolute; bottom: 0; right: 0;">
                <form action="/ideations/{{ $ideation->slug }}/members/{{ $user->id }}/remove" method="post">
                  @csrf
                  @method('delete')
                  <button type="submit" class="btn btn-sm btn-light deleteMemberButton" style="color: #666160;">@lang('general.remove')</button>
                </form>
              </div>
            @endif
          </a>
        @endforeach
      </div>
    </div>
</div>

@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://unpkg.com/vue-multiselect@2.1.0"></script>
  <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
  <script>
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

            $('#inviteUsersForm').submit();
          }
        }
      });

      $('.deleteMemberButton').on('click', function (event) {
        event.preventDefault();
        if(confirm("@lang('ideations.confirm-remove-member')"))
          $(event.target.parentElement).submit();
      });
    </script>
@endsection
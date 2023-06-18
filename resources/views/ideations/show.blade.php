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
<div class="row">
    <div class="col-md-8">
      @if ($errors->any())
          <div class="alert alert-danger">
              <ul>
                  @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                  @endforeach
              </ul>
          </div>
      @endif
      @foreach($ideation->posts as $post)
        <div class="card mb-2">
            <div class="card-body" style="position: relative;">
                @include('ideations.posts.actions')
                <div class="d-flex">
                    <div>
                        <a class="d-block mb-2" href="/users/{{ $post->owner->id }}" style="height: 3em; width: 3em; border-radius: 50%; background-image: url('{{ $post->owner->photo_path }}'); background-size: cover; background-position: center;">
                        </a>
                    </div>
                    <div class="ml-2" style="max-width: 625px; overflow: all; word-wrap: break-word;">
                        <div class="mb-3">
                            <a href="/users/{{ $post->owner->id }}"><b>{{ $post->owner->name }}</b></a><br>
                            <span class="text-muted">{{ $post->created_at->tz($authUser->timezone)->format('M d, Y - g:i a') }}</span>
                        </div>
                        <span style="word-wrap: break-word;">{!! $post->formatted_body !!}</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        <div class="card">
            <div class="card-body">
              @if(!$ideation->is_current_user_participant)
                <div class="text-center">
                  <p><b>@lang('ideations.no-reply-join')</b></p>
                  <form action="/ideations/{{ $ideation->slug }}/join" method="post" class="d-inline-block">
                      @csrf
                      <button type="submit" class="btn btn-primary">@lang('ideations.join-ideation')</button>
                  </form>
                </div>
              @else
                <p><b>@lang('ideations.write-a-reply')</b></p>
                <form action="/ideations/{{ $ideation->slug }}/reply" method="post">
                    @csrf
                    <textarea maxlength="255" class="form-control mb-2" name="body" rows="4" required></textarea>
                    <div class="text-right">
                        <button type="submit" class="btn btn-secondary">@lang('general.post')</button>
                    </div>
                </form>
              @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        @if($ideation->user)
        <form action="/ideations/{{ $ideation->slug }}/leave" method="post" class="d-block w-100 mb-2">
            @csrf
            <button type="submit" class="btn btn-grey w-100">@lang('ideations.leave')</button>
        </form>
        <hr class="my-3">
        @endif
        <div>
            @if($ideation->is_approved)
            <p class="mb-1 text-muted text-center font-weight-bold text-uppercase">@lang('ideations.participants')</p>
            <p class="text-center" style="font-size: 2em;">{{ $ideation->participants()->count() }}/{{ $ideation->max_participants }}</p>
            @else
                @if($ideation->proposer == request()->user())
                <p class="text-center">@lang('ideations.proposer-details')<br>@lang('ideations.proposer-details-continued')</p>
                @endif
                @if(request()->user()->is_admin || request()->user()->groups()->where('group_user.is_admin', '=', 1)->count())
                <p>
                  <b>@lang('ideations.groups-added')</b><br>
                  @foreach($ideation->groups as $group)
                  <a href="/groups/{{ $group->slug }}">{{ $group->name }}</a><br>
                  @endforeach
                </p>
                <a href="/ideations/{{ $ideation->slug }}/review" class="d-block btn btn-secondary">@lang('ideations.review-and-approve')</a>
                <button data-toggle="modal" data-target="#declineModal" class="btn btn-outline-secondary d-block w-100 mt-2">@lang('ideations.decline')</button>

                <div class="modal fade" id="declineModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <form action="/ideations/{{ $ideation->slug }}/decline" method="post" class="w-100 d-block">
                    @csrf
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">@lang('ideations.decline') {{ $ideation->name }}</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                          <div class="form-group">
                            <label for="message">@lang('ideations.reason'): <small class="text-muted">(@lang('general.optional'))</small></label>
                            <input type="text" class="form-control" name="message" id="message">
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="submit" class="btn btn-secondary">@lang('ideations.decline')</button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
                @endif
            @endif
            @foreach($ideation->participants()->limit(5)->get() as $user)
            <a href="/users/{{ $user->id }}" class="d-block card mx-0 mt-1 mb-2 px-1 no-underline">
              <div class="card-body d-flex align-items-center justify-content-start p-0 py-1">
                <div class="d-flex align-items-center justify-content-center">
                  <div class="mr-3 ml-2" style="height: 3em; width: 3em; border-radius: 50%; background-image: url('{{ $user->photo_path }}'); background-size: cover; background-position: center; flex-shrink: 0;">
                  </div>
                  <div class="pt-1">
                    <span class="d-block" style="color: #343a40; font-weight: 600;">{{ $user->name }}</span>
                    <span class="d-block card-subtitle mb-1 text-muted" style="margin-top: 0.005em;">{{ $user->job_title }}</span>
                  </div>
                </div>
              </div>
            </a>
            @endforeach
            @include('ideations.partials.invite', ['ideation' => $ideation])
        </div>
    </div>
</div>
@endsection

@section('scripts')
  <script>
    $('#deleteButton').on('click', function(event) {
      event.preventDefault();
      if (confirm('Delete this entire ideation?'))
        $('#deleteButton').parent().submit();
    });
    $('.deleteButton').on('click', function(event) {
      event.preventDefault();
      if (confirm('Delete this reply?'))
        $(this).parent().submit();
    });
    $('.dismessButton').on('click', function(event) {
      event.preventDefault();
      if (confirm('Dismiss the report on this post?'))
        $(this).parent().submit();
    });
  </script>
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
      })
    </script>
@endsection
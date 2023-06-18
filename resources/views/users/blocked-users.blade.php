@extends('layouts.app')

@section('content')
<div class="bg-lightest-brand">
  <section class="py-3">
    <div class="container-fluid justify-content-center">
      <div class="d-flex justify-content-between">
      @if(!isset($_GET['back']))
        <a href="/home" class="d-inline-block mb-3" style="font-size: 14px;"><i class="icon-chevron-small-left"></i> @if(!$authUser->is_event_only)
          @lang('messages.my-dashboard')
        @else
          @lang('general.groups')
        @endif
        </a>
      @else
        <a href="{{ url()->previous() }}" class="d-inline-block mb-3" style="font-size: 14px;"><i class="icon-chevron-small-left"></i> @lang('messages.back-to') {{ $_GET['back'] }}</a>
      @endif
      @if ($authUser->id != request()->id)
        <a href="" class="d-inline-block mb-3 btn btn-outline-primary text-sm" style="font-size: 14px;">@lang('messages.block-user')</a>
      @endif
      </div>
      <div class="row">
        <div class="col-12 col-md-3 mb-4 mb-md-0">
          <div class="card card-profile-large text-center">
            <div class="card-body mt-2">
                @if($user->id == $authUser->id)
                  <div class="text-center mb-3" style="z-index: 10;">
                    <a href="/profile">@lang('messages.profile.edit')</a>
                  </div>
                @endif
                <div class="mx-auto mb-2 mt-1" style="background-color: #f3f3f3; width: 12em; height: 12em; border-radius: 50%; background-image: url('{{ $user->photo_path }}'); background-size: cover; background-position: center; overflow: hidden;">
                </div>
                @if($authUser->id == $user->id)
                  <a href="/profile#image">@lang('messages.change-image')</a>
                @endif
                <div class="my-3">
                    <div class="mb-2">
                        <h5 class="mb-0">{{ $user->name }}</h5>
                        @if($user->gender_pronouns)
                        <span class="text-small text-muted d-block">{{ $user->gender_pronouns }}</span>
                        @endif
                        <span>{{ $user->job_title }}</span>
                        <span class="d-block text-muted">{{ $user->company }}</span>
                    </div>
                    @if($user->location)
                    <span class="text-small text-muted"><i class="icon-location"></i> {{ $user->location }}</span>
                    @endif
                </div>
                @if ($user->id != $authUser->id)
                <div>
                    <a href="/messages/new?user={{ $user->id }}" class="btn btn-outline-primary w-100 text-sm"><i class="icon-mail"></i> @lang('messages.message')</a>
                </div>
                <div class="mt-2">
                  <a href="/introductions/new?user={{ $user->id }}" class="btn btn-outline-primary w-100 text-sm">@lang('messages.make-an-introduction')</a>
                </div>
                <div class="mt-2">
                  <a href="/video-room/user-{{ $user->id }}" class="btn btn-outline-primary w-100 text-sm">@lang('messages.join-video-room')</a>
                </div>
                @else
                <div class="mt-2 d-flex">
                  <a href="/video-room/user-{{ $user->id }}" class="btn btn-outline-primary w-100 text-sm">@lang('messages.my-video-room')</a>
                  <div class="my-auto ml-1">
                    @include('partials.tutorial', ['tutorial' => \App\Tutorial::where('name', 'Video Rooms')->first()])
                  </div>
                </div>
                @endif
            </div>
          </div>
          @if($user->twitter || $user->facebook || $user->instagram || $user->linkedin || $user->website || $user->id == $authUser->id)
          <div class="card">
            <div class="card-body text-center">
              @if($user->twitter)
              <a style="background-color: {{ getThemeColors()->accent['300'] }}" href="https://twitter.com/{{ $user->twitter }}" target="_blank" class="social-icon-circle mr-1"><i class="socicon-twitter"></i></a>
              @endif
              @if($user->facebook)
              <a style="background-color: {{ getThemeColors()->accent['300'] }}" href="{{ $user->facebook }}" target="_blank" class="social-icon-circle mr-1"><i class="socicon-facebook"></i></a>
              @endif
              @if($user->instagram)
              <a style="background-color: {{ getThemeColors()->accent['300'] }}" href="https://www.instagram.com/{{ $user->instagram }}" target="_blank" class="social-icon-circle mr-1"><i class="socicon-instagram"></i></a>
              @endif
              @if($user->linkedin)
              <a style="background-color: {{ getThemeColors()->accent['300'] }}" href="{{ $user->linkedin }}" target="_blank" class="social-icon-circle mr-1"><i class="socicon-linkedin"></i></a>
              @endif
              @if($user->website)
              <a href="{{ (str_contains($user->website, '//')) ? $user->website : 'http://'.$user->website }}" target="_blank" class="mt-2 d-block">{{ $user->website }}</a>
              @endif

            </div>
          </div>
          @endif
        </div>

        <div class="col-12 col-md-7">
          <div class="card">
            <div class="card-body">
                
                <div class="mb-2 text-left">
                    <h6 class="mb-1 font-weight-bold" style="text-transform: uppercase;">@lang('messages.blocked-users')</h6>
                    @foreach($user->blockedUsers() as $user)
                        <div class="card">
                        <div class="card-body p-1">
                            <div class="ml-1 d-flex align-items-center">
                                <div
                                    style="height: 3em; width: 3em; border-radius: 50%; background-image: url(&quot;{{$user->photo_path}}&quot;); background-size: cover; background-position: center center; flex-shrink: 0;">
                                </div>
                                <div class="ml-3">
                                    <span class="d-block mb-1" style="font-size: 0.85em; color: rgb(52, 58, 64); font-weight: 600;">{{$user->name}}</span>
                                    <a href="/users/{{$user->id}}?unblock=true" class="">Unblock</a>
                                </div>
                            </div>
                        </div>
                        </div>
                    @endforeach
                </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-2 text-center">
          @if(getsetting('is_points_enabled'))
            <div class="text-center mt-1">
              <span class="font-secondary-brand d-block mb-1" style="line-height: 1; font-size: 36px; font-weight: 500;">{{ $user->points_ytd }}</span>
              @if($user->id == $authUser->id)
                <a href="/my-points"><span class="d-block">Points This Year</span></a>
              @else
                <span class="d-block">Points This Year</span>
              @endif
            </div>
            <hr>
          @endif
          <p class="font-weight-bold">@lang('messages.badges')</p>
          <div class="d-flex flex-wrap justify-content-around">
            @foreach($user->allBadges($authUser->id == $user->id) as $badge)
              <div class="mx-2 my-1" data-toggle="tooltip" data-placement="top" title="{{ $badge->description ? $badge->description : $badge->name }}" style="height: 90px;">
                @if(!$badge->getRawOriginal('icon') && !$badge->getRawOriginal('icon_url'))
                  @if($badge->id <= 5)
                    <div style="height: 3em;">@include('badges.'.$badge->id)</div>
                  @else
                    <div style="height: 3em;">@include('badges.default')</div>
                  @endif
                @else
                  <img src="{{ $badge->icon }}" style="height: 3em; max-width: 3em;">
                @endif
                <span class="d-block" style="color: #8f8585;font-weight: 500; font-size: 0.8em; width: 75px; overflow-wrap: break-word;">{{ strlimit($badge->name, 20) }}</span>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </section>
  </div>
  @endsection

  @section('scripts')
  <script>
    $('#showMore').click(function (event) {
      event.preventDefault();
      $('#showMore').addClass('d-none');
      var short = $('.bio-short').text();
      if (short.indexOf('...') > 140)
        $('.bio-short').text(short.slice(0, -3));
        $('.bio-short').append($('#showMore').attr('data-rest'));
    });
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
  </script>
  @endsection

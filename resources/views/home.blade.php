@extends('layouts.app')

@section('stylesheets')
<style>
  .pagination {
    justify-content: center;
  }
</style>
@endsection

@section('content')

<div class="main-container bg-lightest-brand">

  <div class="pt-3">
    <div class="container-fluid">
      @if($virtualRoom)
        <div style="width: 100%; position: relative; text-align: center;">
          <img src="{{ $virtualRoom->image_url }}" style="width: 100%">
          @foreach($virtualRoom->clickAreas as $area)
          <div style="text-align: left; position: absolute; top: {{ $area->y_coor }}; left: {{ $area->x_coor }}; height: {{ $area->height }}; width: {{ $area->width }}; z-index: 100;">
            <a href="{{ $area->target_url }}" target="{{ $area->a_target }}" style="position: absolute; height: 100%; width: 100%"></a>
          </div>
          @endforeach
        </div>
      @elseif($header_image)
        <div class="row">
          <div class="col">
            <img class="mb-3" style="max-height: 700px; width:100%" src="{{ $header_image }}">
          </div>
        </div>
      @endif
      <div class="d-block d-sm-none">
        <div class="row mb-3">
            @if(optional($dashboard_left_nav_image)->value)
            <div class="col-6">
              @if($dashboard_left_nav_image_link)
                <a href="{{ $dashboard_left_nav_image_link->value }}" {{ $does_dashboard_left_nav_image_open_new_tab->value ? 'target="blank"' : ''}}><img class="mb-2" style="width: 100%" src="{{ \App\Setting::where('name', 'dashboard_left_nav_image')->first()->value }}"></a>
              @else
                <img class="mb-2" style="width: 100%" src="{{ \App\Setting::where('name', 'dashboard_left_nav_image')->first()->value }}">
              @endif
            </div>
            @endif
          @if(false)
          <div class="col-12">
            <div class="d-block d-sm-none d-ios-block mb-2 d-flex flex-column align-items-end justify-content-around">
              <a href="/browse" class="btn btn-primary mt-1 d-block w-100">{{ getsetting('find_your_people_alias') }}</a>
              <a href="/my-groups" class="btn btn-primary mt-1 d-block w-100">@lang('messages.my-groups')</a>
              <a href="/calendar" class="btn btn-primary mt-1 d-block w-100">@lang('general.events')</a>
            </div>
            @if(getsetting('is_points_enabled'))
            <div class="text-center mt-3">
              <span class="font-secondary-brand d-block" style="line-height: 1; font-size: 36px; font-weight: 500;">{{ $authUser->points_ytd }}</span>
              <a href="/my-points"><span class="d-block">Points This Year</span></a>
            </div>
            @endif
          </div>
          @endif
        </div>
      </div>

      <div class="row">
        <div class="col-sm-3 d-ios-none">
          <div class="d-none d-md-block d-ios-none">
            @if($dashboard_left_nav_image)
              @if($dashboard_left_nav_image_link)
                <a href="{{ $dashboard_left_nav_image_link }}" {{ $does_dashboard_left_nav_image_open_new_tab ? 'target="blank"' : ''}}><img class="mb-2" style="width: 100%" src="{{ \App\Setting::where('name', 'dashboard_left_nav_image')->first()->value }}"></a>
              @else
                <img class="mb-2" style="width: 100%" src="{{ \App\Setting::where('name', 'dashboard_left_nav_image')->first()->value }}">
              @endif
            @endif
            @include('partials.homepagenav')
          </div>
          @if(getSetting('is_ask_a_mentor_enabled'))
          <div class="d-none d-sm-block d-ios-none bg-light-secondary-brand p-3 mt-3">
            <p class="font-weight-bold">{{ getsetting('ask_a_mentor_alias') }}</p>
            <p>@lang('messages.mentor-description')</p>
            <a href="/mentors/ask" class="btn btn-primary">@lang('messages.check-it-out') <i class="icon-controller-play ml-1"></i></a>
          </div>
          @endif
          <div class="pt-3 d-none d-sm-block d-ios-none">
            <h5 style="font-size: 1.05em;">@lang('general.latest-articles')</h5>
            <div id="latestArticles">
              @foreach($twoWeekOldArticles as $article)
                <div class="mb-3">
                  <a href="{{ $article->post->url }}" data-redirect="/article/{{ $article->post->id }}" class="no-underline" style="background-color: #f7d0c9; background-image: url('{{ $article->post->image_url }}'); background-size: cover; background-position: center; height: 12em; display: flex; align-items: flex-end;" target="_blank">
                    <div class="d-block px-2 py-1 mb-2" style="background-color: #fff; width: 85%; color: #1e1e20; border-left: 5px solid {{ getThemeColors()->accent['300'] }}; transform: translateX(-5px);">
                    {{ $article->post->title }}
                    <br>
                    @if($article->group)
                      <span class="d-block" style="line-height: 1.2; font-size: 12px; font-weight: bold; color: {{ getThemeColors()->primary['400'] }};">{{ $article->group->name }}</span>
                    @endif
                    </div>
                </a>
              </div>
              @endforeach
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-ios-12">

          @each('partials.feed', $posts, 'post', 'partials.empty')

          <div class="text-center">
            {{ $posts->links() }}
          </div>
        </div>

        <div class="col-sm-3 col-ios-12">
          @if(getsetting('is_points_enabled'))
          <div class="text-center pb-3 my-2" style="border-bottom: 1px solid hsla(220, 25%, 85%, 1)">
            <span class="font-secondary-brand d-block" style="line-height: 1; font-size: 48px; font-weight: 500;">{{ $authUser->points_ytd }}</span>
            <a href="/my-points"><span class="d-block">@lang('messages.home.points')</span></a>
          </div>
          @endif
          <div class="text-center pb-3 mb-3 mt-1" style="border-bottom: 1px solid hsla(220, 25%, 85%, 1)">
            <h5 style="font-size: 1em;">@lang('messages.badges')</h5>
            <div class="row flex-wrap">
              <div class="d-flex flex-wrap justify-content-around">
              @foreach($authUser->allBadges(true) as $badge)
              <div class="mx-2 my-1 text-center" data-toggle="tooltip" data-placement="top" title="{{ $badge->description ? $badge->description : $badge->name }}" style="height: 90px;">
                @if(!$badge->icon == null && $badge->icon == '/')
                  <div style="height: 3em;">@include('badges.default')</div>
                @else
                  <img src="{{ ltrim($badge->icon , '/') }}" style="height: 3em; max-width: 75px;">
                @endif
                <span class="d-block" style="color: #8f8585;font-weight: 500; font-size: 0.8em; width: 75px; overflow-wrap: break-word;">{{ strlimit($badge->name, 20) }}</span>
              </div>
            @endforeach
            </div>
            </div>
          </div>

          <div>
            <div class="bg-light-secondary-brand py-2 px-2 mb-3">
              <p class="font-weight-bold">@lang('messages.home.people')</p>
            </div>

              @foreach($authUser->mostPopulatedOptions->take(6) as $category)
              <div class="mb-4">
                  <h6 style="text-transform: uppercase;">{{ $category->name }}</h6>
                  @foreach($category->activeUsers()->where('users.id', '!=', request()->user()->id)->inRandomOrder()->take(3)->get() as $result)
                    <a href="/users/{{ $result->id }}" class="card mb-2 px-1 no-underline">
                      <div class="card-body p-1">
                        <div class="ml-1 d-flex align-items-center">
                          <div style="height: 3em; width: 3em; border-radius: 50%; background-image: url('{{ $result->photo_path }}'); background-size: cover; background-position: center; flex-shrink: 0;">
                          </div>
                          <div class="ml-3">
                            <span class="d-block mb-1" style="font-size: 0.85em; color: #343a40; font-weight: 600;">{{ $result->name }}</span>
                            <span class="d-block card-subtitle mb-1 text-muted" style="font-size: 0.85em; line-height: 1.2;">{{ $result->job_title }}</span>
                          </div>
                        </div>
                      </div>
                    </a>
                @endforeach
                <div class="text-center">
                  <a href="/browse/?options[0]={{ $category->id }}">{{ getsetting('find_your_people_alias') }}</a>
                </div>
              </div>
              @endforeach
          </div>
        </div>
      </div>
    </div>
    <!--end of container-->
</div>
</div>
@endsection

@section('scripts')
<script>
  $.ajax({
    url: "/users/timezone",
    type: "POST",
    data: {
      _token: '{{ csrf_token() }}',
      timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
    }
  });
</script>
@endsection

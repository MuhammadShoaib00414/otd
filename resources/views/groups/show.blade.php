@extends('groups.layout')

@section('body-class', 'col-md-9')

@section('stylesheets')
@parent
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog==" crossorigin="anonymous" />
<style>
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  .spinner {
    border: 5px solid #f3f3f3;
    border-top: 5px solid {{ getThemeColors()->primary['200'] }};
    border-radius: 50%;
    width: 50px;
    height: 50px;
    margin: auto;
    animation: spin 2s linear infinite;
  }
  .pagination {
    justify-content: center;
  }
  .chat-container {
    padding: 1em;
    position: absolute;
    height: 100%;
    width: 100%;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    display: flex;
    justify-content: flex-end;
    align-items: flex-start;
  }
  @media (max-width: 800px) {
    .chat-container {
      position: relative;
      max-height: 400px;
    }
  }
  @if($group->header_bg_image_path)
    .group-header-bg {
      background-color: #565d6b;
      background-image: url('{{ $group->header_bg_image_url  }}');
      background-size: cover;
      background-position: center center;
    }
    .group-header-sizer {
      width: 100%;
      padding-top: 23%;
    }
  @else
    .group-header-bg {
      background-color: #565d6b;
    }
  @endif
</style>
@endsection

@section('header-content')
  @if($room)
    <div style="width: 100%; position: relative; text-align: center;">
      <div style="width: 100%; position: relative; text-align: center;">
        <img src="{{ $room->image_url }}" style="width: 100%">
        @foreach($room->clickAreas as $area)
        <div style="text-align: left; position: absolute; top: {{ $area->y_coor }}; left: {{ $area->x_coor }}; height: {{ $area->height }}; width: {{ $area->width }}; z-index: 100;">
          <a href="{{ $area->target_url }}" target="{{ $area->a_target }}" style="position: absolute; height: 100%; width: 100%"></a>
        </div>
        @endforeach
      </div>
      <div class="chat-container">
        @if($group->zoom_meeting_id)
          <x-video-room :room="$group->videoRoom" type="floating" :chat="optional($group->chatRoom)->is_live" :group="$group"></x-video-room>
        @endif
        @if(optional($group->chatRoom)->is_live && !$group->should_live_chat_display_below_header_image)
          <x-live-chat :room="$group->chatRoom" type="floating" :video="optional($group->videoRoom)->is_enabled"></x-live-chat>
        @endif
      </div>

    </div>
  @else
    @if($group->has_home_image)
      <div class="group-header-bg" style="background-image: url('{{ $group->header_bg_image_url }}');">
        <div class="group-header-sizer"></div>
      </div>
    @endif
  @endif
@endsection

@section('inner-content')
<div class="row">
  <div class="col-md-8">
    @if(getsetting('show_join_button_on_group_pages') && !$group->is_private && $group->is_joinable && !$group->users()->where('user_id', request()->user()->id)->exists())
      <form action="/groups/{{ $group->slug }}/join" method="post" class="d-lg-none d-md-none">
        @csrf
        <button type="submit" class="btn btn-primary w-100 mb-3">@lang('groups.Join group')</button>
      </form>
    @endif

    @if($group->is_sequence_visible_on_group_dashboard && $group->sequence && getsetting('is_sequence_enabled') && $group->is_sequence_enabled)
        <div id="carouselExampleControls" class="carousel slide" data-ride="carousel" data-interval="false">
          <div class="carousel-inner">
            @foreach($modules as $module)
              <a {{ ($module->is_available) ? 'href=/groups/'.$group->slug.'/sequence/modules/'.$module->id : '' }} class="carousel-item{{ ($loop->first) ? ' active' : '' }}">
                <img src="{{ $module->thumbnail_image_path }}" class="d-block w-100" alt="{{ $module->name }}"{!! (!$module->is_available) ? ' style="filter: grayscale(1);"' : '' !!}>
                @if($module->hasUserCompleted($authUser))
                  <div style="position: absolute; top: 0; left: 0; height: 100%; width: 100%; border: 5px solid #26c126;"></div>
                  <i class="fas fa-check-circle" style="color: #26c126; position: absolute; bottom: 2rem; right: 2rem; font-size: 3em;"></i>
                @endif
              </a>
            @endforeach
          </div>
          <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
          </a>
          <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
          </a>
        </div>
    @endif
    <div class="d-flex justify-content-between align-items-center my-3">
      <h3 class="font-weight-bold mb-0">
          @lang('messages.recent-activity')
      </h3>
      @if(($group->is_posts_enabled || $group->is_events_enabled || $group->is_shoutouts_enabled || $group->is_content_enabled) && ($group->isUserAdmin($authUser->id) || ($group->can_users_post_events || $group->can_users_post_text || $group->can_users_post_content || $group->can_users_post_shoutouts || $group->can_users_post_discussions)))
        <div>
          <div class="btn-group" style="max-width: 400px;">
            @if(($group->is_discussions_enabled || $group->is_posts_enabled || $group->is_events_enabled || $group->is_shoutouts_enabled || $group->is_content_enabled) && ($group->isUserAdmin($authUser->id) || $group->can_users_post_text || $group->can_users_post_discussions))
              <a class="btn btn-sm btn-secondary px-2" href="/groups/{{ $group->slug }}/posts/select-type">
                @lang('messages.new') @lang('general.post')
              </a>
            @endif
          </div>
        </div>
      @endif
    </div>
    @if($pinned_post)
        @include('partials.feed', ['post' => $pinned_post, 'group' => $group, 'pinned' => true])
    @endif
    @if(count($posts) > 0 || $pinned_post != null)
        @foreach($posts as $post)
            @include('partials.feed', ['post' => $post, 'group' => $group])
        @endforeach
    @else
        @include('partials.empty')
    @endif

    <div class="text-center">
      {{ $posts->links() }}
    </div>
  </div>
  <div class="col-md-4">
    @if($group->isUserAdmin($authUser->id) && $group->reported_posts->count())
      <a href="/groups/{{ $group->slug }}/flagged">
        <div class="bg-white p-2 mb-3 d-flex" style="border-top: 3px solid #d03232;">
          <span class="badge badge-danger pb-1 pt-1 px-2">{{ $group->reported_posts->count() }}</span><p class="font-weight-bold ml-1">@lang('messages.flagged-posts')</p>
        </div>
      </a>
    @endif

    @if(optional($group->videoRoom)->is_enabled && !($group->is_virtual_room_enabled && $group->virtualRoom && $group->virtualRoom->image_path) && is_zoom_enabled() && $group->zoom_meeting_id)
      <x-video-room :room="$group->videoRoom" type="inline" :chat="optional($group->chatRoom)->is_live" :group="$group"></x-video-room>
    @endif
    @if(optional($group->chatRoom)->is_live && ($group->should_live_chat_display_below_header_image || !($group->is_virtual_room_enabled && $group->virtualRoom && $group->virtualRoom->image_path)))
      <x-live-chat :room="$group->chatRoom" type="inline" :video="optional($group->videoRoom)->is_enabled"></x-live-chat>
    @endif
    @if($group->is_events_enabled && $group->upcoming_events->count())
      <div class="bg-light-secondary-brand pt-3 px-3 pb-2 mb-3">
        <h5 style="font-size: 1em;">@lang('messages.events.upcoming.plural')</h5>
        @foreach($group->upcoming_events as $event)
          <a href="/groups/{{ $group->slug }}/events/{{ $event->id }}" class="d-flex justify-content-between my-2">
            <div class="col pl-0">
              <b aria-title="{!! $event->name !!}">{!! $event->name !!}</b><br>
              {{ $event->date->tz(request()->user()->timezone)->format('m/d/y - g:i a') }}
            </div>
            <div data-start-at="{{ $event->date->tz(request()->user()->timezone)->toIso8601String()  }}" data-end-at="{{ $event->end_date->tz(request()->user()->timezone)->toIso8601String()  }}" class="upcoming_event {{ $event->is_live ? '' : 'd-none' }}">
              @include('groups.events.live')
            </div>
          </a>
        @endforeach
      </div>
    @endif
    <div class="mt-3">
      @if(getsetting('show_join_button_on_group_pages') && !$group->is_private && $group->is_joinable && !$group->users()->where('user_id', request()->user()->id)->exists())
        <form action="/groups/{{ $group->slug }}/join" method="post" class="d-sm-none d-md-block">
          @csrf
          <button type="submit" class="btn btn-primary w-100 mb-3">@lang('groups.Join group')</button>
        </form>
      @endif
      <div class="d-flex justify-content-between">
        <p class="font-weight-bold mb-2">{{ $group->members_page }}</p>
        <a href="/groups/{{ $group->slug }}/members" class="font-weight-bold" style="font-size: 14px;">@lang('messages.view-all')</a>
      </div>
      @foreach($group->activeUsers()->orderBy('name', 'asc')->limit(12)->distinct()->get() as $user)
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
        <div class="text-center">
          <a href="/groups/{{ $group->slug }}/members">@lang('messages.all') {{ $group->members_page }} <i class="icon-chevron-small-right"></i></a>
        </div>
      </div>
    </div>
  </div>
</div>

{!! $group->embed_code !!}
@endsection

@section('scripts')
<script>
   @if($group->is_sequence_visible_on_group_dashboard && $group->sequence && getsetting('is_sequence_enabled') && $group->is_sequence_enabled)
    $('#carouselExampleControls').carousel({{ $last_available_module_index }});
  @endif
@if($group->upcoming_events->count())
  setInterval(checkUpcomingEvents, 600);
  function checkUpcomingEvents()
  {
    $('.upcoming_event').each(function(e) {
      var now = new Date(); 
      var start = new Date($(this).data('start-at'));
      var end = new Date($(this).data('end-at'));
      if(start < now && end > now)
        $(this).removeClass('d-none');
      else if(!$(this).hasClass('d-none'))
        $(this).addClass('d-none');
    });
  }
@endif
</script>
@endsection
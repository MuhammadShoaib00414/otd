@extends('groups.layout')

@section('stylesheets')
@parent
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog==" crossorigin="anonymous" />
<style>
  .vid-container {
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
    .vid-container {
      position: relative;
      max-height: 400px;
    }
  }
  .chat-container.chat-style {
    position: relative;
    padding: 0px;
    height: 23%;
    display: contents;
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
  .group-header-bg {
    background-color: {{ getsetting('group_header_color') }}; 
  }
</style>
@endsection

@section('content')
    <div class="group-header-bg">
        <div class="mx-md-auto">
          <div class="text-center py-3">
            <h3 style="color:white;">{{ $lounge->name }}</h3>
          </div>
        </div>
    </div>
    <div class="px-3 mt-3">
      <div class="row my-2" style="height: 0.5em;">
        @include('groups.partials.backlinks', ['group' => $group])
      </div>
        <div class="row">

            <div class="col-md-2">
              
               @if($group->enable_video_conference_in_lounge)
                <div class="chat-container chat-style" id="move_div">
                  <x-video-room :room="$lounge->videoRoom" type="floating" :chat="null" :hideCloseButton="true" :group="$lounge"></x-video-room>
                </div>
                @endif
              
                <div class="mb-2">
                    <a href="/browse?group={{ $group->id }}" class="btn btn-sm btn-primary" style="white-space: normal;">@lang('lounge.Network using keywords for a perfect match')</a>
                </div>
             
                @if(optional($lounge->chatRoom)->is_live)
                    <x-live-chat :room="$lounge->chatRoom" type="inline" :video="false"></x-live-chat>
                @endif
            </div>
            
            <div class="col-md-8">
              @if($room)
              <div style="width: 100%; position: relative; text-align: left;" id="cover-div">
                <img src="{{ $room->image_url }}" style="width: 100%">
                @foreach($room->clickAreas as $area)
                <div style="position: absolute; top: {{ $area->y_coor }}; left: {{ $area->x_coor }}; height: {{ $area->height }}; width: {{ $area->width }}; z-index: 100;">
                  <a href="{{ $area->target_url }}" target="{{ $area->a_target }}" style="position: absolute; height: 100%; width: 100%"></a>
                </div>
                @endforeach
              </div>
              
              @elseif($group->enable_video_conference_in_lounge)
                <x-video-room :room="$lounge->videoRoom" type="full" :chat="null" :hideCloseButton="true" :group="$lounge"></x-video-room>
              @endif
            </div>
            <div class="col-md-2">
          <div>
            <div class="bg-light-secondary-brand py-2 px-2 mb-3">
              <p class="font-weight-bold">@lang('general.people_you_should_know')</p>
            </div>
              @foreach(App\Option::withCount(['users' => function ($q) use ($group) { $q->whereIn('users.id', $group->users()->pluck('id')); }])->get()->sortByDesc('users_count')->take(3) as $category)
              <div class="mb-4">
                  <h6 style="text-transform: uppercase;">{{ $category->name }}</h6>
                  @foreach($category->activeUsers()->where('users.id', '!=', $authUser->id)->whereIn('users.id', $group->users()->pluck('id'))->inRandomOrder()->take(3)->get() as $result)
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
                  <a href="/browse/?options[0]={{ $category->id }}&group={{ $group->id }}">@lang('general.browse_all')</a>
                </div>
              </div>
              @endforeach
          </div>
            </div>
        </div>
    </div>
@endsection
<script>
    function changePostion() {
      // div width
    var width = document.getElementById('cover-div').clientWidth;
    var height = document.getElementById('cover-div').clientHeight;
    console.log('height',height);
     document.getElementById("videoConferenceContainer").style.position = "absolute";
     document.getElementById("videoConferenceContainer").style.top = "0%";
     document.getElementById("videoConferenceContainer").style.left = "106%";
     document.getElementById("videoConferenceContainer").style.transform = "translate(0%, 0%)";
     document.getElementById("move_div").style.position = "absolute";
     document.getElementById("videoConferenceContainer").style.width = width + "px";
     document.getElementById("videoConferenceContainer").style.height = height + "px";
 
}

</script>
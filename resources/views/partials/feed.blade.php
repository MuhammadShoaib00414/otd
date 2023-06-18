@push('stylestack')
<style>
  .discussion-post-body figure {
    text-align: center;
  }

  .discussion-post-body img {
    max-width: 100%;
    max-height: 280px;
    margin-left: auto;
    margin-right: auto;
  }
</style>
@endpush

@if($post->post instanceOf \App\TextPost)
<div class="card mb-2">
  <div class="card-body">
    <div class="pb-2" style="border-bottom: 1px solid #e9ecef;">
      <div class="d-flex justify-content-between">
        <div class="col-8">
          @if(!$post->posted_as_group_id && $post->post->user_id)
          <div class="row">
            <a href="/users/{{ $post->post->user->id }}" class="d-flex no-underline font-dark">
              <div class="mr-2" style="height: 2.25em; width: 2.25em; min-height: 32px; min-width: 32px; border-radius: 50%; background-image: url('{{ $post->post->user->photo_path }}'); background-size: cover; background-position: center;">
              </div>
              <div>
                <span class="d-block font-weight-bold" style="line-height: 1;">{{ $post->post->user->name }}
                  @if(isset($group) && $group->isUserAdmin($post->post->user->id, false))
                  <span class="badge ml-1" style="background-color: {{ getThemeColors()->primary['300'] }};">Group Admin</span>
                  @endif
                </span>
                <span style="line-height: 1;">{{ $post->post->user->job_title }}</span>
              </div>
            </a>
          </div>
          @elseif(!$post->post->user_id)
          <div class="row">
            <div class="d-flex no-underline font-dark mr-2">
              <div class="mr-2" style="height: 2.25em; width: 2.25em; border-radius: 50%; background-image: url('{{ getsetting('logo') }}'); background-size: cover; background-position: center;">
              </div>
              <span style="color: {{ getThemeColors()->primary['400'] }}; font-size: 1.1em;"><b style="vertical-align: middle;">{{ getsetting('name') }}</b></span>
            </div>
          </div>
          @else
          <a style="color:{{ getThemeColors()->primary['400'] }};" href="/groups/{{ $post->posted_by_group->slug }}"><b>{{ $post->posted_by_group->name }}</b></a>
          @endif
        </div>
        <div class="col-sm-3 mr-2">
          <div class="text-right" style="line-height: 1.3;">
            {{ $post->post_at->tz(request()->user()->timezone)->format('M d, Y') }}
            <br>
            {{ $post->post_at->tz(request()->user()->timezone)->format('g:i a') }}
          </div>
        </div>
        @include('groups.posts.actions', ['group' => (isset($group)) ? $group : $post->getGroupFromUser(request()->user()->id)])
      </div>
    </div>
    @if($post->photo_path)
  </div>
  <div style="margin-top: -1em;">
    <img style="width:100%;" src="{{ $post->photo_url }}">
  </div>
  @if($post->post->content)
  <div class="card-body">
    @endif
    @endif
    @if($post->post->content)
    <div class="pt-3 redactor-output" data-postid="{{ $post->id }}">
      {!! str_replace(['"//www.youtube.com', '"https://www.youtube.com'], '"https://youtube.com', $post->post->content) !!}
    </div>
  </div>
  @endif
  @if($post->post->custom_links)
  <div class="row px-3 justify-content-center mb-2">
    @foreach($post->post->custom_links as $link)
    <div class="col-md-6 mb-2">
      <a style="overflow: hidden;" target="_blank" href="{{ $link['url'] }}" class="d-block btn btn-light postLinkButton" data-postid="{{ $post->id }}"><span style="color:#3e4e63;">{{ $link['title'] }} <span class="sr-only"> @lang('messages.for') {{ $post->post->name }}</span></span></a>
    </div>
    @endforeach
  </div>
  @endif
  @include('components.post-footer', ['pinned' => isset($pinned), 'post' => $post])
</div>
@elseif($post->post instanceOf \App\Shoutout && $post->post->shouted)
<div class="card mb-2">
  <div class="card-body">
    @include('groups.posts.actions', ['group' => (isset($group)) ? $group : $post->getGroupFromUser(request()->user()->id)])
    <div class="pb-2" style="border-bottom: 1px solid hsla(220, 25%, 85%, 1)">
      <div class="d-flex justify-content-between">
        <div>
          @if(!$post->posted_as_group_id)
          <a href="/users/{{ $post->post->shouting->id }}" class="d-flex no-underline font-dark">
            <div class="mr-2" style="height: 2.25em; width: 2.25em; border-radius: 50%; min-height: 32px; min-width: 32px; background-image: url('{{ $post->post->shouting->photo_path }}'); background-size: cover; background-position: center;">
            </div>
            <div>
              <span class="d-block font-weight-bold" style="line-height: 1;">{{ $post->post->shouting->name }}
                @if(isset($group) && $group->isUserAdmin($post->post->shouting->id, false))
                <span class="badge ml-1" style="background-color: {{ getThemeColors()->primary['300'] }};">Group Admin</span>
                @endif</span>
              <span style="line-height: 1;">{{ $post->post->shouting->job_title }}</span>
            </div>
          </a>
          @else
          <a style="color:{{ getThemeColors()->primary['400'] }};" href="/groups/{{ $post->posted_by_group->slug }}"><b>{{ $post->posted_by_group->name }}</b></a>
          @endif
        </div>
        <div class="text-right mr-3" style="line-height: 1.3;">
          {{ $post->post->created_at->tz(request()->user()->timezone)->format('M d, Y') }}
          <br>
          {{ $post->post->created_at->tz(request()->user()->timezone)->format('g:i a') }}
        </div>
      </div>
    </div>
    <div class="pt-3">
      <p>{{ (isset($group) && $group->shoutouts_page != 'Shoutouts') ? $group->shoutouts_page . ' ' . __('messages.to_lc') : __('shoutouts.shoutout_to') }} <i class="icon-megaphone"></i></p>
      <a href="/users/{{ $post->post->shouted->id }}" class="d-block text-center mb-3 light-hover-bg font-dark py-2 mx-5">
        <div class="mb-2 mx-auto" style="height: 5.25em; width: 5.25em; border-radius: 50%;  min-height: 32px; min-width: 32px; background-image: url('{{ $post->post->shouted->photo_path }}'); background-size: cover; background-position: center;">
        </div>
        <div class="text-center">
          <span class="d-block font-weight-bold" style="line-height: 1;">{{ $post->post->shouted->name }}
            @if(isset($group) && $group->isUserAdmin($post->post->shouted->id, false))
            <span class="badge ml-1" style="background-color: {{ getThemeColors()->primary['300'] }};">Group Admin</span>
            @endif</span>
          <span style="line-height: 1;">{{ $post->post->shouted->job_title }}</span>
        </div>
      </a>
      @if($post->post->shouting)
      <div class="text-center mb-3">
        <a href="/messages/new?user={{ $post->post->shouted->id}}&message=RE Shoutout from {{ $post->post->shouting->name }}, Congratulations" class=" btn btn-sm btn-primary">@lang('messages.say-congrats')</a>
      </div>
      @endif
      <p>{{ $post->post->body }}</p>
    </div>
  </div>
  @include('components.post-footer', ['pinned' => isset($pinned), 'post' => $post])
</div> 
@elseif($post->post instanceOf \App\Event && !$post->post->deleted_at)
<div class="card">
  <div class="card-body">
    <div class="card-body d-flex justify-content-between pb-2" style="border-bottom: 1px solid rgb(233, 236, 239);">
     
        <div class="row">
          <a href="/users/{{ $post->post->user->id }}" class="d-flex no-underline font-dark">
            <div class="mr-2" style="height: 2.25em; width: 2.25em; min-height: 32px; min-width: 32px; border-radius: 50%; background-image: url('{{ $post->post->user->photo_path }}'); background-size: cover; background-position: center;">
            </div>
            <div>
              <span class="d-block font-weight-bold" style="line-height: 1;">{{ $post->post->user->name }}
                @if(isset($group) && $group->isUserAdmin($post->post->user->id, false))
                <span class="badge ml-1" style="background-color: {{ getThemeColors()->primary['300'] }};">Group Admin</span>
                @endif
              </span>
              <span style="line-height: 1;">{{ $post->post->user->job_title }}</span>
            </div>
          </a>
        </div>
     
      <div class="text-right" style="line-height: 1.3;">

        <div class="text-right" style="line-height: 1.3;">
          {{ $post->post_at->tz(request()->user()->timezone)->format('M d, Y') }}
          <br>
          {{ $post->post_at->tz(request()->user()->timezone)->format('g:i a') }}
        </div>

      </div>
    </div>

    @if(request()->is('*home'))
    @include('partials.actions', ['group' => false])
    @else
    @include('groups.posts.actions', ['group' => (isset($group)) ? $group : $post->getGroupFromUser(request()->user()->id)])
    @endif
    <div>

      <div class="row {{ $post->post->is_live ? '' : 'justify-content-center' }}">
        @if($post->post->is_live)
        <div class="pl-2 pb-2">
          @include('groups.events.live')
        </div>
        @endif
        <h5 style="{{ $post->post->is_live ? 'position: absolute;margin-left: auto;margin-right: auto;left: 0;right: 0;text-align: center;' : '' }} font-size: 1em;" class="font-secondary-brand font-weight-bold">@if($post->post->has_happened && !$post->post->is_live && !$post->post->is_cancelled)
          @lang('messages.events.previous')
          @elseif(!$post->post->is_cancelled)
          @lang('messages.events.upcoming')
          @else
          Cancelled
          @if($post->post->cancelled_reason)
          @lang('messages.events.cancelled.reason', ['reason' => $post->post->cancelled_reason])
          @else
          @lang('messages.event')
          @endif
          @endif</h5>
      </div>
      @if($post->post->image)
      <a class="row" href="/groups/{{ $post->getGroupFromUser(request()->user()->id)->slug }}/events/{{ $post->post->id }}"><img src="{{ $post->post->image_path }}" class="mb-3 mx-md-auto" style="max-width: 100%; max-height:500px;"></a>
      @endif
      <p class="font-weight-bold">{{ $post->post->name }}</p>
      <p>{{ $post->post->date->tz(request()->user()->timezone)->format('m/d/y @ g:i a') }} <small class="text-muted">({{ request()->user()->timezone }})</small></p>
      <div class="row px-3 justify-content-center">
        <div class="col-md-6 mb-2">
          <a href="/groups/{{ $post->getGroupFromUser(request()->user()->id)->slug }}/events/{{ $post->post->id }}" class="d-block btn btn-outline-primary font-size-sm-sm">@lang('messages.events.details')</a>
        </div>
        @if($post->post->custom_links)
        @foreach($post->post->custom_links as $link)
        <div class="col-md-6 mb-2">
          <a style="overflow: hidden;" target="_blank" href="{{ $link['url'] }}" class="d-block btn btn-light"><span class="font-size-sm-sm" style="color:#3e4e63;">{{ $link['title'] }} <span class="sr-only"> @lang('messages.for') {{ $post->post->name }}</span></span></a>
        </div>
        @endforeach
        @endif
      </div>
    </div>
  </div>
  @include('components.post-footer', ['pinned' => isset($pinned), 'post' => $post])
</div>
@elseif($post->post instanceOf \App\ArticlePost)
<div class="card">
  <a href="{{ $post->post->url }}" target="{{ ($post->post->code == null) ? '_blank' : '_self' }}" class="text-center article" style="background-color: #fff;">
    @if(!$post->post->is_video)
    <img src="{{ $post->post->image_url }}" class="card-img-top" style="max-height: 22em; width: auto; max-width: 100%;">
    @else
    <iframe src="{{ $post->post->embedded_video }}" style="height: 22em; width: 100%;"></iframe>
    @endif
  </a>
  <div class="card-body">
    @include('groups.posts.actions', ['group' => (isset($group)) ? $group : $post->getGroupFromUser(request()->user()->id)])
    <a href="{{ $post->post->is_video ? '/watch?' . http_build_query(['v' => $post->post->embedded_video]) : $post->post->url }}" target="{{ ($post->post->code == null) ? '_blank' : '_self' }}">
      <h5 class="card-title">{{ $post->post->title }}</h5>
    </a>
  </div>
  @include('components.post-footer', ['pinned' => isset($pinned), 'post' => $post])
</div>
@elseif($post->post instanceOf \App\DiscussionThread && $post->group)
<div class="card mb-2">
  <div class="card-body pb-2">
    @if(($post->post->group->isUserAdmin($authUser->id) && $post->has_reported))
    <div style="background-color: #d03232; padding: 0.1em 0.5em; border-bottom-left-radius: 4px; border-bottom-right-radius: 4px; position: absolute; top: 0; left: 50%; transform: translateX(-50%); color: #fff;">
      @lang('general.reported')
    </div>
    @endif
    <div class="pb-2" style="border-bottom: 1px solid hsla(220, 25%, 85%, 1)">
      <div class="d-flex justify-content-between">
        <div>
          <a href="/users/{{ $post->post->owner->id }}" class="d-flex no-underline font-dark">
            <div class="mr-2" style="height: 2.25em; width: 2.25em; border-radius: 50%; min-height: 32px; min-width: 32px; background-image: url('{{ $post->post->owner->photo_path }}'); background-size: cover; background-position: center;">
            </div>
            <div>
              <span class="d-block font-weight-bold" style="line-height: 1;">{{ $post->post->owner->name }}
                @if(isset($group) && $group->isUserAdmin($post->post->owner->id, false))
                <span class="badge ml-1" style="background-color: {{ getThemeColors()->primary['300'] }};">Group Admin</span>
                @endif
              </span>
              <span style="line-height: 1;">{{ $post->post->owner->job_title }}</span>
            </div>
          </a>
        </div>
        <div class="d-flex justify-content-end">
          <div class="text-right mr-3" style="line-height: 1.3;">
            {{ $post->created_at->tz(request()->user()->timezone)->format('M d, Y') }}
            <br>
            {{ $post->created_at->tz(request()->user()->timezone)->format('g:i a') }}
          </div>
        </div>
      </div>
    </div>
    <div class="pt-3">
      <p class="font-weight-bold">
        <a href="/groups/{{ $post->post->group->slug }}/discussions/{{ $post->post->slug }}/">{{ $post->post->name }}</a>
      </p>
      <div class="discussion-post-body">
        <?php $firstPost = $post->post->posts()->first(); ?>
        @if($firstPost)
        {!! str_replace(['"//www.youtube.com', '"https://www.youtube.com'], '"https://youtube.com', $firstPost->formatted_body) !!}
        @endif
      </div>
      @if($post->post->posts()->count() > 1)
      <hr class="mt-2 mb-1">
      <div style="font-size: 14px;">
        <p class="mb-1"><b>@lang('discussions.Recent Replies')</b></p>
        @foreach($post->post->recent_posts as $comment)
        <div class="ml-1 {{ (!$loop->last) ? ' mb-2' : '' }} discussion-post-body">
          <p class="mb-0"><b>{{ $comment->user->name }}</b></p>
          {!! $comment->formatted_body !!}
        </div>
        @endforeach
      </div>
      @endif
      <hr class="my-2">
      <div class="text-center" style="font-size: 14px;">
        <a href="/groups/{{ $post->post->group->slug }}/discussions/{{ $post->post->slug }}/">@lang('general.view')</a>
        <span>&centerdot;</span>
        <a href="/groups/{{ $post->post->group->slug }}/discussions/{{ $post->post->slug }}#reply">@lang('messages.reply')</a>
      </div>
    </div>
  </div>
  @include('components.post-footer', ['pinned' => isset($pinned), 'post' => $post])
</div>
@endif
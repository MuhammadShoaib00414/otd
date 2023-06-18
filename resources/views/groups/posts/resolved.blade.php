@extends('groups.layout')

@section('body-class', 'col-md-9')

@section('stylesheets')
@parent
<style>
  .pagination {
    justify-content: center;
  }
</style>
@endsection

@section('inner-content')
<div class="row">
  <div class="col-md-8">
    @if (count($posts) > 0)
        @foreach ($posts as $post)
          @if($post->post)
            @if($post->post_type == 'App\DiscussionThread')

            @else
            <small class="text-muted">@lang('posts.resolved_by') <a href="/users/{{ $post->reported->resolved_by }}">{{ $post->reported->resolved_by_user->name }}</a></small>
            @endif
            @include('partials.feed', ['post' => $post, 'group' => $group])
          @endif
        @endforeach
    @else
        @include('partials.empty')
    @endif
  </div>
  <div class="col-md-4">
    <div class="bg-light-secondary-brand pt-3 px-3 pb-2">
      <p class="font-weight-bold">@lang('events.upcoming_events')</p>
      @foreach($group->upcoming_events as $event)
        <a href="/groups/{{ $group->slug }}/events/{{ $event->id }}" class="d-block my-2">
          <b>{!! $event->name !!}</b><br>
          {{ $event->date->tz(request()->user()->timezone)->format('m/d/y - g:i a') }}
        </a>
      @endforeach
    </div>
    <div class="mt-4">
      <div class="d-flex justify-content-between">
        <p class="font-weight-bold mb-2">{{ $group->members_page }}</p>
        <a href="/groups/{{ $group->slug }}/members" class="font-weight-bold" style="font-size: 14px;">@lang('general.view_all')</a>
      </div>
      @foreach($group->users()->orderBy('name', 'asc')->limit(12)->get() as $user)
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
          <a href="/groups/{{ $group->slug }}/members">All {{ $group->members_page }} @lang('posts.flagged_posts') <i class="icon-chevron-small-right"></i></a>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
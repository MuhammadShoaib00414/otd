@extends('groups.layout')

@section('inner-content')
      <div>
          <h5 class="card-title">@lang('general.new')</h5>
           @if($group->is_discussions_enabled && ($group->isUserAdmin($authUser->id) || $group->can_users_post_discussions))
            <a href="/groups/{{ $group->slug }}/discussions/create" class="block card">
              <div class="card-body font-weight-bold">
                 {{ $group->discussions_page }}
              </div>
            </a>
          @endif

          @if($group->is_posts_enabled && ($group->isUserAdmin($authUser->id) || $group->can_users_post_text))
            <a href="/groups/{{ $group->slug }}/posts/new" class="block card">
              <div class="card-body font-weight-bold">
                {{ $group->posts_page }}
              </div>
            </a>
          @endif

          @if($group->is_events_enabled && ($group->isUserAdmin($authUser->id) || $group->can_users_post_events))
            <a href="/groups/{{ $group->slug }}/events/new" class="block card">
              <div class="card-body font-weight-bold">
                {{ $group->calendar_page }}
              </div>
            </a>
          @endif

          @if($group->is_shoutouts_enabled && ($group->isUserAdmin($authUser->id) || $group->can_users_post_shoutouts))
            <a href="/groups/{{ $group->slug }}/shoutouts/new" class="block card">
              <div class="card-body font-weight-bold">
                 {{ $group->shoutouts_page }}
              </div>
            </a>
          @endif

          @if($group->is_content_enabled && ($group->isUserAdmin($authUser->id) || $group->can_users_post_content))
            <a href="/groups/{{ $group->slug }}/content/add" class="block card">
              <div class="card-body font-weight-bold">
                 {{ $group->content_page }}
              </div>
            </a>
          @endif

      </div>
@endsection


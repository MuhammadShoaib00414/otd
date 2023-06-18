@extends('groups.layout')

@section('inner-content')
  <div class="d-flex justify-content-between align-items-center mb-2">
    <h3 class="font-weight-bold mb-0">{{ $group->posts_page }} ({{ $group->textPosts()->count() }})</h3>
    @if($group->isUserAdmin($authUser->id))
    <a href="/groups/{{ $group->slug }}/posts/new" class="btn btn-sm btn-secondary"><i class="icon-plus"></i> @lang('posts.new_post')</a>
    @endif
  </div>

  @forelse($posts as $post)
    @include('partials.feed', ['post' => $post])
  @empty
    @include('partials.empty')
  @endforelse

    @if($group->can_group_admins_schedule_posts)
    <div>

    </div>
    @endif
@endsection

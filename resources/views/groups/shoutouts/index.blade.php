@extends('groups.layout')

@section('inner-content')
  <div class="d-flex justify-content-between align-items-center mb-2">
    <div class="d-flex">
      <h3 class="font-weight-bold mb-0 mr-2">{{ $group->shoutouts_page }} ({{ $group->shoutouts()->count() }})</h3>
      <div class="my-auto">
        @include('partials.tutorial', ['tutorial' => \App\Tutorial::where('name', 'Shoutouts')->first()])
      </div>
    </div>
    @if($group->is_shoutouts_enabled && ($group->isUserAdmin($authUser->id) || $group->can_users_post_shoutouts))
      <a href="/groups/{{ $group->slug }}/shoutouts/new" class="btn btn-sm btn-secondary"><i class="icon-plus"></i> @lang('messages.new-shoutout')</a>
    @endif
  </div>

  @forelse($group->shoutouts()->orderBy('posts.post_at', 'desc')->get() as $post)
  @if($post->post)
    <div class="card mb-2">
      <div class="card-body p-0">
        @include('groups.posts.actions')
        <div class="p-2" style="border-bottom: 1px solid hsla(220, 25%, 85%, 1)">
          <div class="d-flex justify-content-between">
            {{-- @if(!$post->posted_as_group_id) --}}
            <a href="/users/{{ $post->post->shouting->id ?? '' }}" class="d-flex no-underline font-dark">
              <div class="mr-2" style="height: 2.25em; width: 2.25em; border-radius: 50%; background-image: url('{{ $post->post->shouting->photo_path ?? '' }}'); background-size: cover; background-position: center;">
              </div>
              <div>
                <span class="d-block font-weight-bold" style="line-height: 1;">{{ $post->post->shouting->name ?? '' }}</span>
                <span style="line-height: 1;">{{ $post->post->shouting->job_title ?? '' }}</span>
              </div>
            </a>
            {{-- @else
            <a style="color:#f29181;" href="/groups/{{ $post->posted_by_group->slug }}"><b>{{ $post->posted_by_group->name ?? '' }}</b></a>
            @endif --}}
            <div class="text-right mr-3" style="line-height: 1.3;">
              {{ $post->post->created_at->tz(request()->user()->timezone)->format('M d, Y') }}
              <br>
              {{ $post->post->created_at->tz(request()->user()->timezone)->format('g:i a') }}
            </div>
          </div>
        </div>
        <div class="p-2">
          <p>{{ (isset($group) && $group->shoutouts_page != 'Shoutouts') ? $group->shoutouts_page . ' ' . __('messages.to_lc') : __('shoutouts.shoutout_to') }}</p>
          <a href="/users/{{ $post->post->shouted->id }}" class="d-block text-center mb-3 light-hover-bg font-dark py-2 mx-5">
            <div class="mb-2 mx-auto" style="height: 5.25em; width: 5.25em; border-radius: 50%; background-image: url('{{ $post->post->shouted->photo_path ?? '' }}'); background-size: cover; background-position: center;">
            </div>
            <div class="text-center">
              <span class="d-block font-weight-bold" style="line-height: 1;">{{ $post->post->shouted->name ?? ''  }}</span>
              <span style="line-height: 1;">{{ $post->post->shouted->job_title ?? '' }}</span>
            </div>
          </a>
          <p>{{ $post->post->body }}</p>
        </div>
        @include('components.post-footer', ['pinned' => isset($pinned), 'post' => $post])
        @if($group->isUserAdmin($authUser->id) || $authUser->id == $post->post->shouting->id)
          <!-- <div class="d-flex justify-content-end p-2" style="border-top: 1px solid #e9ecef;">
            <form action="/groups/{{ $group->slug }}/posts/{{ $post->id }}/delete" method="post">
              @csrf
              @method('delete')
              <button type="submit" class="btn btn-sm btn-light deletePostButton" style="color: #666160;">@lang('general.delete')</button>
            </form>
          </div> -->
        @endif
      </div>
    </div>
    @endif
  @empty
    @include('partials.empty')
  @endforelse
@endsection

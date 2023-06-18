@if(($post->isUserAdmin($authUser->id) && $post->is_reported) || ($post->reported_by == request()->user()->id && !$post->resolved_by))
  <div style="background-color: #d03232; padding: 0.1em 0.5em; border-bottom-left-radius: 4px; border-bottom-right-radius: 4px; position: absolute; top: 0; left: 50%; transform: translateX(-50%); color: #fff;">
      @lang('general.reported')
  </div>
@endif
<div class="dropdown" style="position: absolute; top: 1em; right: 1em;">
  <button class="btn btn-sm dropdown-toggle dropdownMenuButton" style="background-color: #fff;" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
    @if(($post->isUserAdmin($authUser->id) || $authUser->is_admin) && request()->is('*groups*') && $post->getGroupFromUser(request()->user()->id))
        @if(isset($pinned))
            <form method="post" action="/groups/{{ ($group) ? $group->slug : $post->getGroupFromUser(request()->user()->id)->slug }}/posts/{{ $post->id }}/pin">
                @csrf
                <button type="submit" class="dropdown-item hover-hand">@lang('unpin')</button>
            </form>
        @else
            <form method="post" action="/groups/{{ ($group) ? $group->slug : $post->getGroupFromUser(request()->user()->id)->slug }}/posts/{{ $post->id }}/pin">
                @csrf
                <button type="submit" class="dropdown-item hover-hand">@lang('posts.pin')</button>
            </form>
        @endif
    @endif
    @if(isset($group) && $group->isUserAdmin(request()->user()->id) && $group->can_ga_order_posts && request()->route()->getName() == 'group_home')
        <form method="post" action="/groups/{{ $group->slug }}/posts/{{ $post->id }}/moveUp">
            @csrf
            <button class="dropdown-item hover-hand">@lang('posts.move up')</button>
        </form>
        <form method="post" action="/groups/{{ $group->slug }}/posts/{{ $post->id }}/moveDown">
            @csrf
            <button class="dropdown-item hover-hand">@lang('posts.move down')</button>
        </form>
    @endif
    @if(optional($post->post)->user_id == request()->user()->id || $authUser->is_admin)
    <a href="{{ request()->is('*home') || !$post->getGroupFromUser(request()->user()->id) ? '/posts/'.$post->id.'/edit' : '/groups/'. $post->getGroupFromUser(request()->user()->id)->slug .'/posts/'. $post->id .'/edit' }}" class="dropdown-item hover-hand editButton">@lang('general.edit')</a>
    @endif
    @if($post->isUserAdmin($authUser->id) && $group != null)
    <form action="{{ request()->is('*home') ? '' : '/groups/'. $group->slug }}/posts/{{ $post->id }}" method="post">
        @csrf
        @method('delete')
        <button type="submit" class="dropdown-item hover-hand deleteButton">@lang('general.delete')</button>
    </form>
    @endif
    @if($post->post->user_id != request()->user()->id)
    <form action="/posts/{{ $post->id }}/report" method="post">
        @csrf
        @method('put')
        <button onclick="return confirm('Report this post?')" class="dropdown-item reportButton">@lang('general.report')</button>
    </form>
    @endif
    @if($post->is_reported && $post->isUserAdmin($authUser->id))
    <form action="/groups/{{ $post->getGroupFromUser(request()->user()->id)->slug }}/posts/{{ $post->id }}/resolve" method="post">
        @csrf
        @method('put')
        <button class="dropdown-item dismissButton">@lang('general.dismiss_report')</button>
    </form>
    @endif
  </div>
</div>
@if(($post->isUserAdmin($authUser->id) && $post->is_reported) || ($post->reported_by == request()->user()->id && !$post->resolved_by))
  <div style="background-color: #d03232; padding: 0.1em 0.5em; border-bottom-left-radius: 4px; border-bottom-right-radius: 4px; position: absolute; top: 0; left: 50%; transform: translateX(-50%); color: #fff;">
      @lang('general.reported')
  </div>
@endif
<div class="dropdown" style="position: absolute; top: 1em; right: 1em;">
  <button class="btn btn-sm dropdown-toggle" style="background-color: #fff;" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
    @if($post->user == $authUser || $post->isUserAdmin($authUser->id))
    <a href="/groups/{{ $post->getGroupFromUser(request()->user()->id)->slug }}/discussions/{{ $post->thread->slug }}/posts/{{ $post->id }}/edit" class="dropdown-item">Edit</a>
    @endif
    @if($post->isUserAdmin($authUser->id) || $post->user->id == $authUser->id)
    <form action="/groups/{{ $post->getGroupFromUser(request()->user()->id)->slug }}/discussions/{{ $post->thread->slug }}/posts/{{ $post->id }}/delete" method="post">
        @csrf
        @method('delete')
        <button type="submit" class="dropdown-item hover-hand deleteButton">@lang('general.delete')</button>
    </form>
    @endif
    @if(!$post->is_reported)
    <form action="/groups/{{ $post->getGroupFromUser(request()->user()->id)->slug }}/discussions/{{ $post->thread->slug }}/posts/{{ $post->id }}/flag" method="post">
        @csrf
        <button onclick="return confirm('Report this post?')" class="dropdown-item reportButton">@lang('general.report')</button>
    </form>
    @endif
    @if($post->is_reported && $post->isUserAdmin($authUser->id))
    <form action="/groups/{{ $post->getGroupFromUser(request()->user()->id)->slug }}/discussions/{{ $post->thread->slug }}/posts/{{ $post->id }}/resolve" method="post">
        @csrf
        <button class="dropdown-item dismissButton">@lang('general.dismiss-report')</button>
    </form>
    @endif
  </div>
</div>
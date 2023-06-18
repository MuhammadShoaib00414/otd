@if(($post->isUserAdmin($authUser->id) && $post->is_reported) || ($post->reported_by == request()->user()->id && !$post->resolved_by))
  <div style="background-color: #d03232; padding: 0.1em 0.5em; border-bottom-left-radius: 4px; border-bottom-right-radius: 4px; position: absolute; top: 0; left: 50%; transform: translateX(-50%); color: #fff;">
      @lang('general.reported')
  </div>
@endif
<div class="dropdown" style="position: absolute; top: 1em; right: 1em;">
  <button class="btn btn-sm dropdown-toggle dropdownMenuButton" style="background-color: #fff;" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
  <div class="dropdown-menu dropdown-menu-right multi-level" aria-labelledby="dropdownMenuButton">
    @if($post->post->user_id == request()->user()->id)
    <a href="{{ '/posts/'.$post->id.'/edit' }}" class="dropdown-item hover-hand editButton">@lang('general.edit')</a>
    @endif
    @if($post->post->user_id != request()->user()->id)
    <form action="/posts/{{ $post->id }}/report" method="post">
        @csrf
        <button onclick="return confirm('Report this post?')" class="dropdown-item reportButton">@lang('general.report')</button>
    </form>
    @endif
    @if($post->is_reported && $post->isUserAdmin($authUser->id))
    <form action="/posts/{{ $post->id }}/resolve" method="post">
        @csrf
        <button class="dropdown-item dismissButton">@lang('general.dismiss_report')</button>
    </form>
    @endif
    @if($post->isUserAdmin($authUser))
      @if($post->groups()->count() > 1)
        <h6 class="dropdown-header">@lang('general.delete from')...</h6>
        @foreach($post->groups as $group)
          <form action="/groups/{{ $group->slug }}/posts/{{ $post->id }}/delete" method="post">
              @csrf
              @method('delete')
              <button type="submit" class="dropdown-item hover-hand deleteButton" onclick="return confirm('Are you sure you want to remove this event from {{ $group->name }}?');">{{ $group->name }}</button>
          </form>
        @endforeach
        <form action="/posts/{{ $post->id }}/delete" method="post">
            @csrf
            @method('delete')
            <button type="submit" class="dropdown-item hover-hand deleteButton">@lang('general.delete from all groups')</button>
        </form>
      @else
        <form action="/posts/{{ $post->id }}" method="post">
            @csrf
            @method('delete')
            <button type="submit" class="dropdown-item hover-hand deleteButton">@lang('general.delete')</button>
        </form>
      @endif
    @endif
  </div>
</div>
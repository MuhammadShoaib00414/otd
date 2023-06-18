@if($ideation->is_current_user_participant)
    @if($post->is_reported)
      <div style="background-color: #d03232; padding: 0.1em 0.5em; border-bottom-left-radius: 4px; border-bottom-right-radius: 4px; position: absolute; top: 0; left: 50%; transform: translateX(-50%); color: #fff;">
          Reported
      </div>
    @endif
    <div class="dropdown" style="position: absolute; top: 1em; right: 1em;">
      <button class="btn btn-sm dropdown-toggle" style="background-color: #fff;" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
        @if($post->isUserAdmin($authUser->id) || ($post->owner && $post->owner->id == request()->user()->id) || $ideation->owner->id == request()->user()->id)
            @if(isset($canEdit) && $canEdit)
            <form action="/ideations/{{ $post->ideation()->withTrashed()->first()->slug }}/posts/{{ $post->id }}/edit" method="get">
                <button type="submit" class="dropdown-item hover-hand editButton">@lang('general.edit')</button>
            </form>
            @endif
        <form action="/ideations/{{ $post->ideation()->withTrashed()->first()->slug }}/{{ isset($type) ? $type : 'posts' }}/{{ $post->id }}/delete" method="post">
            @csrf
            @method('delete')
            <button type="submit" class="dropdown-item hover-hand deleteButton">@lang('general.delete')</button>
        </form>
        @endif
        @if(!$post->is_reported)
        <form action="/ideations/{{ $post->ideation()->withTrashed()->first()->slug }}/{{ isset($type) ? $type : 'posts' }}/{{ $post->id }}/report" method="post">
            @csrf
            @method('put')
            <button onclick="return confirm('@lang('general.report-post-confirm')')" class="dropdown-item reportButton">@lang('general.report')</button>
        </form>
        @endif
        @if($post->is_reported && ($post->isUserAdmin($authUser->id) || $ideation->owner->id == request()->user()->id))
        <form action="/ideations/{{ $post->ideation()->withTrashed()->first()->slug }}/{{ isset($type) ? $type : 'posts' }}/{{ $post->id }}/resolve" method="post">
            @csrf
            @method('put')
            <button class="dropdown-item dismissButton">@lang('general.dismiss-report')</button>
        </form>
        @endif
      </div>
    </div>
@endif
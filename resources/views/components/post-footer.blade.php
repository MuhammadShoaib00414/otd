@once
  @push('stylestack')
    <style>
      .postFooter {
        display: grid;
        grid-template-columns: 0fr 0fr 0fr;
      }
    </style>
  @endpush
@endonce



@if($post->group()->exists() || getsetting('is_likes_enabled') || $pinned)
<div class="card-footer text-muted py-0" style="position: relative; background-color: rgb(249, 250, 251); min-height: 40px;">
    <div class="d-flex align-items-center footer-content">
        <div class="text-center">
            <div class="d-flex align-items-center">
            @if(getsetting('is_likes_enabled'))
                  @include('components.like', ['postable' => $post])
                @else
                  <span></span>
                @endif
               
            </div>
        </div>
        <div class="text-center d-flex align-items-center ml-1">
        @if ($post->post_type != 'App\DiscussionThread' && $post->post_type != "App\DiscussionPost")
      <div data-toggle="collapse" data-parent="#accordion" class="commentSection d-flex align-items-center" href="#collapse_{{$post->id}}" data-post-id = "{{$post->id}}"  style="font-size: 0.7em;">
        <i class="likeButton text-dark icon-message" style="font-size: 1.7em!important"></i> 
        <span id="total_comment">{{$post->comments->count()}}</span> 
      </div>
      @endif
        </div>
        <div class="ml-auto text-center pt-1">
            <div>
                <a href="/groups/this-is-halloween" class="mx-auto">
                    <small>
                        @if($post->group()->exists()) @if($post->group->users()->where('id', request()->user()->id)->exists())
                        <a href="/groups/{{ $post->group->slug }}" class="mx-auto"><small>{{ $post->group->name }}</small></a>
                        @else
                        <a href="#" class="mx-auto"> <small>{{ $post->group->name }}</small></a>
                        @endif @endif
                    </small>
                </a>
            </div>
        </div>
        <span class="ml-2 pt-1">
            @if($pinned) @include('components.pinned') @else
            <span style="width: 68px;"></span>
            @endif
        </span>
    </div>
</div>
<div id="collapse_{{$post->id}}" class="panel-collapse collapse">
    @include('components.comments')
</div>
@endif



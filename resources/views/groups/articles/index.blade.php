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
    <div class="d-flex justify-content-between align-items-center">
      <h3 class="font-weight-bold mb-2">{{ $group->content_page }} <span>({{ $count }})</span></h3>
      @if($group->isUserAdmin($authUser->id))
      <div>
        <button type="button" class="btn btn-outline-secondary btn-sm mr-2" data-toggle="modal" data-target="#export">
          <i class="fas fa-download"></i> Export
        </button>
        <a href="/groups/{{ $group->slug }}/content/add" class="btn btn-sm btn-secondary">
          @lang('articles.Add content')
        </a>
      </div>
      @elseif($group->can_users_post_content))
        <a href="/groups/{{ $group->slug }}/content/add" class="btn btn-sm btn-secondary">@lang('articles.Add content')</a>
      @endif
    </div>

    <div class="row">

        @forelse($posts as $post)
        <div class="col-sm-4 px-2 mb-3">
            <div class="card mb-0">
            
                <a class="article" id="{{ $post->post->id }}" href="{{ $post->post->is_video ? '/watch?' . http_build_query(['v' => $post->post->embedded_video]) : $post->post->url }}" target="{{ ($post->post->code == null) ? '_blank' : '_self' }}" style="display: block; padding-top: 55%; width: 100%; background-color: #eee; background-image: url('{{ $post->post->image_url }}'); background-size: cover; background-position: center;"></a>
                <div class="card-body">
                    <a href="{{ $post->post->is_video ? '/watch?' . http_build_query(['v' => $post->post->embedded_video]) : $post->post->url }}" data-redirect="/article/{{ $post->post->id }}" target="{{ ($post->post->code == null) ? '_blank' : '_self' }}">
                        <h5 class="card-title">{{ $post->post->title }}</h5>
                    </a>
                    @include('groups.posts.actions')
                </div>
            </div>
            @if($group->isUserAdmin($authUser->id))
              <div class="d-flex justify-content-end">
                <form action="/groups/{{ $group->slug }}/posts/{{ $post->id }}/delete" method="post">
                  @csrf
                  @method('delete')
                  <button type="submit" class="btn btn-sm btn-light deletePostButton" style="border-top-left-radius: 0; border-top-right-radius: 0; color: #666160;">@lang('general.delete')</button>
                </form>
              </div>
            @endif
        </div>
        @empty
        </div>
            @include('partials.empty')
        @endforelse

        <div class="text-center w-100">
            {{ $posts->links() }}
        </div>
    </div>


    <div class="modal fade" id="export" tabindex="-1" role="dialog" aria-labelledby="export" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">@lang('articles.Export Content')</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form method="get" action="/groups/{{ $group->slug }}/content/export">
            <div class="modal-body">
              <div class="form-group">
                <label for="start_date">@lang('articles.From') <small class="text-muted">(@lang('general.optional'))</small></label>
                <input type="text" autocomplete="off" class="form-control" name="start_date" id="start_date" placeholder="mm/dd/yy">
              </div>
              <div class="form-group">
                <label for="end_date">@lang('articles.To') <small class="text-muted">(@lang('general.optional'))</small></label>
                <input type="text" autocomplete="off" class="form-control" name="end_date" id="end_date" placeholder="mm/dd/yy">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('general.close')</button>
              <button type="submit" class="btn btn-primary">@lang('articles.Export to CSV')</button>
            </div>
          </form>
        </div>
      </div>
    </div>
@endsection
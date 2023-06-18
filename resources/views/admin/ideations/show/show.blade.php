@extends('admin.ideations.show.layout')

@section('inner-page-content')
<div class="row">
    <div class="col-md-6 mx-md-auto">
      @if ($errors->any())
          <div class="alert alert-danger">
              <ul>
                  @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                  @endforeach
              </ul>
          </div>
      @endif
      @forelse($ideation->posts as $post)
        <div class="card mb-2">
            <div class="card-body" style="position: relative;">
                @include('ideations.posts.actions')
                <div class="d-flex">
                    <div>
                        <a class="d-block mb-2" href="/admin/users/{{ $post->owner->id }}" style="height: 3em; width: 3em; border-radius: 50%; background-image: url('{{ $post->owner->photo_path }}'); background-size: cover; background-position: center;">
                        </a>
                    </div>
                    <div class="ml-2">
                        <div class="mb-3">
                            <a href="/admin/users/{{ $post->owner->id }}"><b>{{ $post->owner->name }}</b></a><br>
                            <span class="text-muted">{{ $post->created_at->tz($authUser->timezone)->format('M d, Y - g:i a') }}</span>
                        </div>
                        {!! $post->formatted_body !!}
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="card mb-2">
          <div class="card-body text-center">
            <span class="my-3">No posts...</span>
          </div>
        </div>
        @endforelse
    </div>
</div>

@endsection
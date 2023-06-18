@extends('ideations.layout')

@section('inner-content')
@if ($errors->any())
    <div class="alert alert-danger">
      <ul>
          @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
      </ul>
    </div>
@endif
    <div class="card">
        <div class="card-body">
            <p><b>Edit your post</b></p>
            <form action="/ideations/{{ $ideation->slug }}/posts/{{ $post->id }}" method="post">
                @csrf
                @method('put')
                <textarea class="form-control mb-2" name="body" rows="4" required>{{ $post->body }}</textarea>
                <div class="text-right">
                    <button type="submit" class="btn btn-secondary">@lang('general.save') changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@extends('groups.layout')

@section('stylesheets')
    @parent
    <link rel="stylesheet" href="/redactorx-1-2-0/redactorx.min.css" />
@endsection

@section('content')
    <div class="mx-auto px-3 mt-4 mb-5" style="max-width: 800px;">
        <p><b>@lang('discussions.Edit your reply')</b></p>
        <form action="/groups/{{ $group->slug }}/discussions/{{ $discussion->slug }}/posts/{{ $post->id }}" method="post">
            @csrf
            @method('put')
            <textarea class="form-control mb-2" name="body" rows="4" id="body">{{ $post->body }}</textarea>
            <div class="text-right mt-3">
                <button type="submit" class="btn btn-secondary">@lang('general.save_changes')</button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script src="/redactorx-1-2-0/redactorx.min.js"></script>

    <script>
        RedactorX('#body', {
            format: false,
            editor: {
                lang: '{{ request()->user()->locale }}',
              },
            image: {
                upload: '/user-api/image-uploader',
                multiple: false,
                url: false
            },
            embed: false,
        });

        $('.rx-container').prop('title', "@lang('posts.Click on the + symbol to add images, text or embed videos')").tooltip('show');

      setInterval(function () {
         $('.rx-container').tooltip('dispose');
      }, 60 * 100);
    </script>
@endsection
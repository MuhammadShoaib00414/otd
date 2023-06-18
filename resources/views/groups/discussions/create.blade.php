@extends('groups.layout')

@section('stylesheets')
    @parent
    <link rel="stylesheet" href="/redactorx-1-2-0/redactorx.min.css" />
    <style>
      input[name="responsive"] {
        display: none;
      }
      input[name="responsive"] + span {
        display: none;
      }
    </style>
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
@endsection

@section('inner-content')
    <a href="/groups/{{ $group->slug }}/discussions" class="d-inline-block mb-2 mt-3" style="font-size: 14px;">
        <i class="icon-chevron-small-left"></i>
        {{ $group->discussions_page ? __('messages.all') . ' ' . str_plural($group->discussions_page) : __('discussions.All Discussions') }}
    </a>
    <div class="d-flex mb-3 justify-content-between align-items-center">
        <h4 class="mb-0">{{ $group->discussions_page ? __('messages.new') . ' ' . $group->discussions_page : __('discussions.New Discussion') }}</h4>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="card" x-data="{ submitted: false }">
        <div class="card-body">
            <form method="post" action="/groups/{{ $group->slug }}/discussions" id="discussionForm">
                @csrf
                <div class="form-group">
                    <label for="title">@lang('discussions.Discussion Title')</label>
                    <input maxlength="80" type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="body">@lang('general.post')</label>
                    <textarea class="form-control" rows="4" name="body" id="body" required></textarea>
                </div>
                <div class="text-right">
                    <button dusk="submit" type="submit" class="btn btn-secondary" id="submitDiscussion">@lang('discussions.save_and_post')</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="/redactorx-1-2-0/redactorx.min.js"></script>
    <script src="/assets/js/es.js"></script>

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
        });

        $('#discussionForm').submit(function (e) {
            $('#submitDiscussion').prop('disabled', true);
        });

        $('.rx-container').prop('title', "@lang('posts.Click on the + symbol to add images, text or embed videos')").tooltip('show');

      setInterval(function () {
         $('.rx-container').tooltip('dispose');
      }, 60 * 100);
    </script>
@endsection
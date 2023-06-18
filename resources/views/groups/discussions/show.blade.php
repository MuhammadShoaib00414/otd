@extends('groups.layout')

@section('body-class', 'col-md-9')

@section('stylesheets')
    @parent
    <style>
        .btn-grey {
            background-color: #dadcdf;
            border-color: #dadcdf;
            color: #645f5f;
        }
        .btn-grey:hover {
            background-color: #ced1d5;
            border-color: #ced1d5;
            color: #645f5f;
        }
        .hover-hand:hover { cursor: pointer; }
        input[name="responsive"] {
            display: none;
        }
        input[name="responsive"] + span {
            display: none;
        }
    </style>
    <link rel="stylesheet" href="/redactorx-1-2-0/redactorx.min.css" />
@endsection

@section('inner-content')
<div class="row mt-3" id="discussionContainer">
    <div class="col-md-8">
        @if ($errors->any())
          <div class="alert alert-danger">
              <ul>
                  @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                  @endforeach
              </ul>
          </div>
        @endif
        <h4 class="mb-2">{{ $discussion->name }}</h4>
        @foreach($discussion->posts as $post)
        <div class="card mb-2">
            <div class="card-body" style="position: relative;">
                @include('groups.discussions.actions')
                <div class="d-flex">
                    <div>
                        <a class="d-block mb-2" href="/users/{{ $post->owner->id }}" style="height: 3em; width: 3em; border-radius: 50%; background-image: url('{{ $post->owner->photo_path }}'); background-size: cover; background-position: center;">
                        </a>
                    </div>
                    <div class="ml-2" style="word-break: break-word;">
                        <div class="mb-3">
                            <a href="/users/{{ $post->owner->id }}"><b>{{ $post->owner->name }} 
                                @if(isset($group) && $group->isUserAdmin($post->owner->id, false))
                                    <span class="badge ml-1" style="background-color: {{ getThemeColors()->primary['300'] }}; color: #485056">Group Admin</span>
                                @endif
                            </b></a><br>
                            <span class="text-muted">{{ $post->created_at->tz($authUser->timezone)->format('M d, Y - g:i a') }}</span>
                        </div>
                    </div>
                </div>
                <div class="redactor-output">
                    {!! str_replace(['"//www.youtube.com', '"https://www.youtube.com'], '"https://youtube.com', $post->formatted_body) !!}
                </div>
            </div>
            @if(getsetting('is_likes_enabled'))
            <div class="card-footer" style="background-color: #f9fafb;">
                @include('components.like', ['postable' => $post])
            </div>
            @endif
        </div>
        @endforeach

        <div id="reply">
            <p class="mb-1"><b>@lang('discussions.Write a reply')</b></p>
            <form action="/groups/{{ $group->slug }}/discussions/{{ $discussion->slug }}/reply" method="post" id="postReplyForm">
                @csrf
                <textarea class="form-control mb-2" name="body" rows="4" id="newPostBody" required></textarea>
                <div class="text-right">
                    <button type="submit" class="btn btn-secondary mt-2" id="postReplyButton">@lang('general.post')</button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-4">
        @if($group->isUserAdmin($authUser->id) || $discussion->owner->id == request()->user()->id)
        <p><b>Options</b></p>
            <a href="/groups/{{ $group->slug }}/discussions/{{ $discussion->slug }}/edit" class="d-block mb-2 btn btn-grey">@lang('general.edit')</a>
            <form method="post" action="/groups/{{ $group->slug }}/discussions/{{ $discussion->slug }}/delete" class="d-block w-100" >
                @method('delete')
                @csrf
                <button type="submit" class="d-block w-100 mb-2 btn btn-grey" id="deleteButton">@lang('general.delete')</button>
            </form>
        @endif
    </div>
</div>
@endsection

@section('scripts')
    <script src="/redactorx-1-2-0/redactorx.min.js"></script>
    <script src="/assets/js/es.js"></script>
    <script>
        $('.redactor-output p a').each(function(link) {
            var href = $(this).attr('href');
            $(this).attr('href', parse_link(href));
        });

        $('#postReplyButton').click(function(e) {
            $(this).prop('disabled', true);
            $('#postReplyForm').submit();
        });

        function parse_link(value)
        {
            if(value.includes('http'))
                return value;
            else
                return 'http://' + value;
        }

        $('#deleteButton').on('click', function(event) {
          event.preventDefault();
          if (confirm("@lang('discussions.Delete this entire discussion thread?')"))
            $('#deleteButton').parent().submit();
        });
        $('.deleteButton').on('click', function(event) {
          event.preventDefault();
          if (confirm("@lang('discussions.Delete this reply?')"))
            $(this).parent().submit();
        });
        RedactorX('#newPostBody', {
            format: false,
            editor: {
                lang: '{{ request()->user()->locale }}',
              },
            image: {
                upload: '/user-api/image-uploader',
                multiple: false,
            }
        });

        $('.rx-container').prop('title', "@lang('posts.Click on the + symbol to add images, text or embed videos')").tooltip('show');

          setInterval(function () {
             $('.rx-container').tooltip('dispose');
          }, 60 * 100);
    </script>
@endsection
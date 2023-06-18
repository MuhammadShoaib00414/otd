@extends('layouts.app')

@section('stylesheets')
  @parent
  <style>
    .pagination {
      justify-content: center;
    }
  </style>
  <link rel="stylesheet" href="/redactorx-1-2-0/redactorx.min.css" />
  <style>
      input[name="responsive"] {
        display: none;
      }
      input[name="responsive"] + span {
        display: none;
      }
    </style>
@endsection

@section('content')

<div class="main-container bg-lightest-brand">

  <section class="pt-3">
    <div class="container-fluid">
      <div class="row justify-content-center">

        <div class="col-12 col-md-8">
          @if ($errors->any())
          <div class="alert alert-danger">
              <ul>
                  @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                  @endforeach
              </ul>
          </div>
          @endif
          <div class="mt-4 mb-5">

              <h5 class="card-title">@lang('messages.edit-post')</h5>
              <form method="post" action="/posts/{{ $post->id }}" enctype="multipart/form-data" id="form-submission">
                @csrf
                @method('put')
                <textarea id="content" rows="8" class="form-control mb-3" name="content" required>{{ $post->post->content }}</textarea>
                <!-- @if($post->photo_path)
                  <img id="img" class="w-100" src="{{ $post->photo_url }}">
                  <hr>
                  <p>Choose a different image:</p>
                @endif
                <input class="form-control-file form-control-lg" name="photo" id="photoUpload" type="file"/> -->
                <div class="text-right mt-3">
                  <button type="submit" class="btn btn-secondary">@lang('general.save')</button>
                </div>
              </form>
          </div>
        </div>

      </div>
    </div>
    <!--end of container-->
</section>
</div>
@endsection

@section('scripts')
  <script src="/redactorx-1-2-0/redactorx.min.js"></script>
  <script>
    // $(document).ready(function(){
    //   $('#photoUpload').change(function(){
    //     $('#content').removeAttr('required');
    //   });
    // });
      RedactorX('#content', {
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
      $('.rx-container').prop('title', "@lang('posts.Click on the + symbol to add images, text or embed videos')").tooltip('show');

      setInterval(function () {
         $('.rx-container').tooltip('dispose');
      }, 60 * 100);
      $('#form-submission').on('click', function (e) {
         localStorage.clear();
      });
  </script>
@endsection

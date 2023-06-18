@extends('groups.layout')

@section('stylesheets')
    @parent
    <link rel="stylesheet" href="/redactorx-1-2-0/redactorx.min.css" />
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
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

@section('body-class', 'col-md-9')

@section('inner-content')
  <div class="d-flex justify-content-between align-items-center mb-2">
    <h3 class="font-weight-bold mb-0">Edit Module</h3>
    <form action="/groups/{{ $group->slug }}/sequence/modules/{{ $module->id }}" method="post">
      @csrf
      @method('delete')
      <button type="submit" class="btn btn-sm btn-outline-secondary" id="deleteButton">Delete</button>
    </form>
  </div>
  
  <form action="/groups/{{ $group->slug }}/sequence/modules/{{ $module->id }}" method="post" id="postForm" enctype="multipart/form-data">
    @csrf

    <div class="card">
      <div class="card-body">

        <div class="form-group">
          <label for="name">Module name</label>
          <input type="text" name="name" id="name" class="form-control" value="{{ $module->name }}" maxlength="70">
        </div>

        <div class="form-row mb-3">
          <div class="col-6">
            <label for="thumbnail" class="d-block">Module thumbnail</label>
            <img src="{{ $module->thumbnail_image_path }}" style="width: 100%; margin-bottom: 1em;">
            <p class="text-sm text-muted">To change, upload a new file:</p>
            <input type="file" name="thumbnail" id="thumbnail">
          </div>
        </div>
        <p class="text-small text-muted">Recommended thumbnail size: Ratio 5:3, Minimum size of 500x300px</p>
      </div>
    </div>

    <div class="mb-3">
      <textarea id="content" rows="8" name="content" required>{{ $module->content }}</textarea>
    </div>
    <div>
      <button id="submitButton" type="submit" class="btn btn-primary">@lang('general.save')</button>
    </div>

  </form>
@endsection

@section('scripts')
  <script src="/redactorx-1-2-0/redactorx.min.js"></script>
  <script src="/assets/js/es.js"></script>
  <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
  <script>
    RedactorX('#content', {
        format: false,
        editor: {
          lang: '{{ request()->user()->locale }}',
        },
        placeholder: "Module content goes here. (Hint: Use the \"+\" icon to add images, embeded videos, or tables)",
        image: {
            upload: '/user-api/image-uploader',
            multiple: false,
            url: false
        },
    });

    $('#postForm').submit(function(){
        $('#submitButton').prop('disabled', true);
    });

    $('#deleteButton').on('click', function(event) {
      event.preventDefault();
      if (confirm('Delete this module?'))
        $('#deleteButton').parent().submit();
    });

    setInterval(function () {
       $('.rx-container').tooltip('dispose');
    }, 60 * 100);
  </script>
@endsection
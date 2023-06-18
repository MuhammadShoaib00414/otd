@extends('admin.layout')

@section('head')
  <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
@endsection

@section('page-content')
  <a href="/admin/content" class="d-inline-block mb-3"><i class="fas fa-angle-left"></i> All Content</a>

  <div class="d-flex justify-content-between" style="max-width: 700px;">
    <h4>Edit</h4>
    <form action="/admin/content/articles/{{ $article->id }}" method="post" class="d-inline-block">
      @method('delete')
      @csrf
      <button type="submit" class="btn btn-light ml-3" id="deleteEvent">Delete</button>
    </form>
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

    <form method="post" action="/admin/content/articles/{{ $article->id }}" style="max-width: 700px; padding-bottom: 3em;" enctype="multipart/form-data">
        @csrf
        @method('put')
        <div class="form-group">
            <label for="title">Title</label>
            <input required type="text" name="title" id="title" value="{{ $article->title }}" class="form-control">
        </div>
        <div class="form-group">
            <label for="url">Content URL</label>
            <input required type="text" name="url" id="url" value="{{ $article->getRawOriginal('url') }}" class="form-control">
        </div>
        <div class="mt-2" id="custom_image">
            <p>Upload your own image:</p>
            @include('components.upload', ['name' => 'custom_image_upload', 'value' => '', 'noRemove' => true])
        </div>
        <div class="form-group mt-4">
            <label for="groups[]"><b>Groups content belongs to</b></label>
            @foreach(App\Group::whereNull('parent_group_id')->orderBy('name', 'asc')->get() as $group)
              @include('admin.posts.partials.groupCheckbox', ['group' => $group, 'checkedGroups' => $article->listing->groups])
            @endforeach
        </div>
        <div class="form-group" style="max-width: 650px;">
                <label for="postDate"><b>Post date</b></label>
                <div class="form-row mb-3">
                  <div class="col">
                    <label>Date</label>
                    <input type="text" name="date" class="form-control" required value="{{ $article->listing->post_at->format('m/d/y') }}" placeholder="mm/dd/yy" id="date">
                  </div>
                  <div class="col">
                    <label>Time</label>
                    <input type="text" name="time" class="form-control" required value="{{ $article->listing->post_at->tz($authUser->timezone)->format('g:i a') }}" placeholder="hh:mm pm" id="time">
                  </div>
                </div>
            </div>
        <button type="submit" class="btn btn-primary">@lang('general.save') changes</button>
    </form>
@endsection

@section('scripts')
<script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<script>
    $('#date').datepicker({
      uiLibrary: 'bootstrap4',
      format: 'mm/dd/yy'
    });
    $('#time').timepicker({
        timeFormat: 'h:mm p',
        dropdown: false,
    });

    $('#deleteEvent').on('click', function(event) {
      event.preventDefault();
      if (confirm('Delete this content?'))
        $('#deleteEvent').parent().submit();
    });
  </script>
@endsection

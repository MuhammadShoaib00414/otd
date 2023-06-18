@extends('admin.layout')

@section('head')
  <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
@endsection

@section('page-content')
    <a href="/admin/content" class="d-inline-block mb-3"><i class="fas fa-angle-left"></i> All Content</a>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="d-flex justify-content-between">
        <h5>Add content</h5>
    </div>
    
    <form action="/admin/content/articles/" method="post" id="form" enctype="multipart/form-data">
        @csrf
        <div class="d-flex">
            <span class="alert alert-danger d-none w-25" id="invalidUrl">URL is invalid.</span>
        </div>
        <div class="form-group">
            <div class="d-flex align-items-center">
                <div class="input-group" style="max-width: 500px;">
                    <input type="text" class="form-control" name="url" id="url" placeholder="URL">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-primary" id="fetchInfo">Fetch</button>
                    </div>
                </div>
                <div class="ml-3">
                    <i class="fa fa-spinner fa-pulse fa-1x fa-fw d-none"></i>
                </div>
            </div>
            <div class="card my-4" style="max-width: 650px;">
                <div class="card-body">
                    <div class="d-flex">
                        <img src="" id="image" class="mr-3" style="height: 100px;">
                        <div>
                            <p id="title" class="mb-0" style="font-size: 1.3em; font-weight: bold;"></p>
                            <a id="pageurl" href="#" class="text-muted"></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card my-4 d-none" id="details_box" style="max-width: 650px;">
                <div class="card-body">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" id="title" name="title" class="form-control">
                    </div>
                    <input type="hidden" id="imageurl" name="image" class="form-control">
                    <img src="" id="image" style="width: 100%;">
                    <div class="mt-2" id="custom_image">
                        <p>Upload your own image:</p>
                        @include('components.upload', ['name' => 'custom_image_upload', 'value' => '', 'noRemove' => true])
                    </div>
                </div>
            </div>
            <div class="form-group" style="max-width: 650px;">
                <label for="postDate"><b>Time to post</b></label>
                <div class="form-row mb-3">
                  <div class="col">
                    <label>Date</label>
                    <input type="text" name="date" class="form-control" required value="{{ old('date') ?: \Carbon\Carbon::now()->format('m/d/y') }}" placeholder="mm/dd/yy" id="date">
                  </div>
                  <div class="col">
                    <label>Time</label>
                    <input type="text" name="time" class="form-control" required value="{{ old('time') ?: '10:00 am' }}" placeholder="hh:mm pm" id="time">
                  </div>
                </div>
            </div>
            <div class="form-group mt-4">
                <label for="groups[]"><b>Groups to add to</b></label>
                @foreach(App\Group::whereNull('parent_group_id')->orderBy('name', 'asc')->get() as $group)
                  @include('admin.posts.partials.groupCheckbox', ['group' => $group])
                @endforeach
            </div>
            <button type="submit" class="btn btn-primary">Add content</button>
        </div>
    </form>
@endsection

@section('scripts')
<script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<script>
    var hasFetched = false;
    $('#fetchInfo').on('click', function () {
        $('.fa-spinner').removeClass('d-none');
        hasFetched = true;
        $.ajax({
            url: '/admin/content/articles/fetch',
            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                url: $('#url').val(),
            },
            success: function (response) {
                if(response == '')
                {
                    $('.fa-spinner').addClass('d-none');
                    $('#invalidUrl').removeClass('d-none');
                }
                else
                {
                    $('#invalidUrl').addClass('d-none');
                    $('.fa-spinner').addClass('d-none');
                    $('#title').text(response.title);
                    $('#image').attr('src', response.image);
                    $('#pageurl').text(response.url).attr('href', response.url);
                    $('#url').val(response.url);
                    $('input[name="title"]').val(response.title);
                    $('input[name="image"]').val(response.image);
                    $('#details_box').removeClass('d-none');
                }
            },
            error: function (response) {
                console.log(response);
            }
        });
    });

    $('#form').submit(function(e) {
        if(hasFetched)
            return true;
        else
            return false;
    });

    $('#date').datepicker({
      uiLibrary: 'bootstrap4',
      format: 'mm/dd/yy'
    });
    $('#time').timepicker({
        timeFormat: 'h:mm p',
        dropdown: false,
    });
  </script>
@endsection
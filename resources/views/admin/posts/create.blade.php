@extends('admin.layout')

@push('stylestack')
    <link rel="stylesheet" href="/redactorx-1-2-0/redactorx.min.css" />
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
  	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
  	<style>
  		.rx-toolbar-container{
  			z-index: 0;
  		}
  	</style>
@endpush

@section('page-content')
<div class="col-10">
	<h4 class="mb-3">New Post</h4>
  @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
	<form action="/admin/posts" method="post" enctype="multipart/form-data">
		@csrf
		<label for="post_type">Post Type</label>
		<div class="d-flex justify-content-between mb-3" style="max-width: 200px;">
			<div class="form-check">
			  <input class="form-check-input" type="radio" name="post_type" id="post_type_text" checked value="text">
			  <label class="form-check-label" for="post_type_text">
			    Text Post
			  </label>
			</div>
			<div class="form-check">
			  <input class="form-check-input" type="radio" name="post_type" id="post_type_content" value="content">
			  <label class="form-check-label" for="post_type_content">
			    Content
			  </label>
			</div>
		</div>

		<div id="text_post">
			<textarea id="message" name="message" class="form-control" rows="8"></textarea>
		</div>

		<div id="content_post" class="d-none">
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
        <div id="custom_image" class="d-none mb-4">
            <p>Upload your own image: <small class="text-muted">(optional)</small></p>
            @include('components.upload', ['name' => 'custom_image_upload', 'value' => '', 'noRemove' => true])
        </div>
        <input type="hidden" name="title">
        <input type="hidden" name="image">
		</div>

		<hr>

		<label>Send to users: </label>
		<x-users-query></x-users-query>

		<hr>

		<div class="form-group mt-3" style="max-width: 650px;">
            <label for="postDate"><b>Time to post</b></label>
            <div class="form-row mb-3">
              <div class="col">
                <label>Date</label>
                <input type="text" name="date" class="form-control" required value="{{ \Carbon\Carbon::now()->tz(request()->user()->timezone)->format('m/d/y') }}" placeholder="mm/dd/yy" id="date">
              </div>
              <div class="col">
                <label>Time</label>
                <input type="text" name="time" class="form-control" required value="{{ \Carbon\Carbon::now()->tz(request()->user()->timezone)->format('g:i a') }}" placeholder="hh:mm pm" id="time">
              </div>
            </div>
        </div>
		<div class="form-group mt-4">
            <label for="groups[]"><b>Groups to add to</b></label>
            @foreach(App\Group::whereNull('parent_group_id')->orderBy('name', 'asc')->get() as $group)
              @include('admin.posts.partials.groupCheckbox', ['group' => $group])
            @endforeach
        </div>
        <button type="submit" class="btn btn-primary mb-5 mt-3">@lang('general.save')</button>
	</form>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
  <script src="/redactorx-1-2-0/redactorx.min.js"></script>
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
                $('.fa-spinner').addClass('d-none');
                $('#title').text(response.title);
                $('#image').attr('src', response.image);
                $('#pageurl').text(response.url).attr('href', response.url);
                $('#url').val(response.url);
                $('input[name="title"]').val(response.title);
                $('input[name="image"]').val(response.image);
                $('#custom_image').removeClass('d-none');
            },
            error: function (response) {
                console.log(response);
            }
        });
    });
    // $(document).ready(function(){
    //   $('#photoUpload').change(function(){
    //     $('#content').removeAttr('required');
    //   });
    // });
    $('#post_type_text').change(function(e) {
  			$('#content_post').addClass('d-none');
    		$('#text_post').removeClass('d-none');
    });
    $('#post_type_content').change(function(e) {
    		$('#text_post').addClass('d-none');
    		$('#content_post').removeClass('d-none');
    });
      RedactorX('#message', {
          format: false,
          image: {
              upload: '/user-api/image-uploader',
              multiple: false,
          },
      });

      $('input[name="custom_image_upload"]').change(function(e) {
        if($(this).val())
        {
            $('#image').addClass('d-none');
        }
        else
        {
            $('#image').removeClass('d-none');
        }
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
@extends('groups.layout')

@section('stylesheets')
@parent
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.min.css" />
@endsection

@section('inner-content')
    <div class="mt-4">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(session()->has('invalid-image'))
            <div class="alert alert-danger">
                {{ session('invalid-image') }}
            </div>
        @endif
    
        <div id="error_display" class="d-none">
            <span class="alert alert-danger">Invalid image url</span>
        </div>
          <div class="card">
            <div class="card-body">
                <div class="d-flex">
                  <h5 class="card-title mr-2 mb-0">@lang('articles.Add content')</h5>
                  @include('partials.tutorial', ['tutorial' => \App\Tutorial::where('name', 'Posting Content')->first()])
                </div>
                <small class="text-muted mb-2">@lang('articles.Videos from Youtube, Vimeo or Facebook will be embedded.')</small>
              <form action="/groups/{{ $group->slug }}/content" method="post" id="form" enctype="multipart/form-data" autocomplete="off">
                @csrf
                <div class="form-group">
                    <div class="d-flex align-items-center">
                        <div class="input-group">
                            <input type="text" class="form-control" name="url" id="url" placeholder="URL" required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-secondary" id="fetchInfo">@lang('articles.Fetch')</button>
                            </div>
                        </div>
                        <div class="ml-3">
                            <i class="fa fa-spinner fa-pulse fa-1x fa-fw d-none"></i>
                        </div>
                    </div>
                    <div class="card my-4" style="max-width: 650px;">
                        <div class="card-body">
                            <div class="form-group">
                                <label>@lang('articles.title')</label>
                                <input type="text" id="title" name="title" class="form-control">
                            </div>
                            <input type="hidden" id="imageurl" name="image" class="form-control">
                            <img src="" id="image" style="width: 100%;">
                            <div class="d-none mt-2" id="custom_image">
                                <p>@lang('articles.Or upload your own:')</p>
                                @include('components.upload', ['name' => 'custom_image_upload', 'value' => '', 'noRemove' => true])
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-4">
                        <label for="groups[]"><b>Groups content belongs to</b></label>
                        @foreach(App\Group::whereNull('parent_group_id')->where('is_content_enabled',1)->orderBy('name', 'asc')->get() as $group)

                        @include('admin.posts.partials.groupCheckboxContent', ['group' => $group])
                        @endforeach
                    </div>
                    <div class="form-group" style="max-width: 650px;">
                        <label for="postDate"><b>Post date</b></label>
                        <div class="form-row mb-3">
                            <div class="col">
                                <label>Date</label>
                                <input type="text" name="date" class="form-control" value="" placeholder="mm/dd/yy" id="date">
                            </div>
                            <div class="col">
                                <label>Time</label>
                                <input type="text" name="time" class="form-control" value="" placeholder="hh:mm pm" id="time">
                            </div>
                        </div>

                    </div>
                    <div class="text-center">
                        <button type="submit" id ="btn" class="btn btn-secondary">@lang('articles.Add content')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
 
@section('scripts')
<script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<script>
     $('#date').on('click', function(e) {
         e.preventDefault();
        $(this).attr("autocomplete", "off");  
        });
       $('#date').datepicker({
       
      uiLibrary: 'bootstrap4',
      format: 'mm/dd/yy'
    }); 
    $(document).ready(function(){
    // Get value on button click and show alert
     $("#btn").click(function(){
        var date = $("#date").val();
        var time = $("#time").val();
        if(date.length > 0 || time.length > 0){
          $('#date').attr("required", true);
          $('#time').attr("required", true);
        }else{
          $('#time').attr("required", false);
          $('#date').attr("required", false);
        }
      });
    });
    $('#date').datepicker({
        uiLibrary: 'bootstrap4',
        format: 'mm/dd/yy'

    });
    $('#time').timepicker({
        timeFormat: 'h:mm p',
        dropdown: false,
    });
    $('#url').on('keypress', function(e) {
        if (e.which == 13) {
            e.preventDefault();
            $('#fetchInfo').trigger('click');
        }
    });
    var hasFetched = false;
    $('#fetchInfo').on('click', function() {
        $('.fa-spinner').removeClass('d-none');
        hasFetched = true;
        $.ajax({
            url: '/articles/fetch',
            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                url: $('#url').val(),
            },
            success: function(response) {
                $('.fa-spinner').addClass('d-none');
                $('#title').val(response.title);
                $('#imageurl').val(response.image);
                $('#image').attr('src', response.image);
                $('#url').val(response.url);
                if (!response.is_video)
                    $('#custom_image').removeClass('d-none')
            },
            error: function(response) {
                $('#error_display').removeClass('d-none');
            }
        });
    });

    $('#form').submit(function(e) {
        if (hasFetched)
            return true;
        else
            return false;
    });

    $('input[name="custom_image_upload"]').change(function(e) {
        if ($(this).val()) {
            $('.image_url').addClass('d-none');
            $('#image').addClass('d-none');
        } else {
            $('.image_url').removeClass('d-none');
            $('#image').removeClass('d-none');
        }
    });
</script>
@endsection
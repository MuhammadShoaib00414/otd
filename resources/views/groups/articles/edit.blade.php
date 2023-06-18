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
    <div class="card">
        <div class="card-body">
            <div class="d-flex">
                <h5 class="card-title mr-2">@lang('articles.Edit content')</h5>
            </div>
            <form action="/groups/{{ $group->slug }}/content/{{ $article->id }}" method="post" id="form" enctype="multipart/form-data" autocomplete="off">
                @csrf
                @method('put')
                <div class="form-group">
                    <div class="card my-4" style="max-width: 650px;">
                        <div class="card-body">
                            <div class="form-group">
                                <label>@lang('articles.title')</label>
                                <input type="text" id="title" name="title" class="form-control" value="{{ $article->title }}">
                            </div>
                            <div class="form-group image_url">
                                <label>@lang('articles.Image')</label>

                                <img src="{{ $article->image_url }}" style="max-width: 100%">
                            </div>
                            <img src="" id="image" style="width: 100%;">
                            <div class="mt-2" id="custom_image">
                                <p>@lang('articles.Change image')</p>
                                @include('components.upload', ['name' => 'custom_image_upload', 'value' => '', 'noRemove' => true, 'accept' => 'image/png, image/jpg, image/jpeg'])
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-4">
                        <label for="groups[]"><b>Groups content belongs to</b></label>
                        @foreach($groups as $group) @include('admin.posts.partials.getShareableGroups', ['group' => $group, 'checkedGroups' => $article->listing->groups]) @endforeach
                    </div>
                    <div class="form-group" style="max-width: 650px;">
                        <label for="postDate"><b>Post date</b></label>
                        <div class="form-row mb-3">
                            <div class="col">
                                <label>Date</label>
                                <input type="text" name="date" class="form-control" required value="{{ $article->listing->post_at->format('m/d/y') }}" placeholder="mm/dd/yy" id="date" />
                            </div>
                            <div class="col">
                                <label>Time</label>
                                <input type="text" name="time" class="form-control" required value="{{ $article->listing->post_at->tz($authUser->timezone)->format('g:i a') }}" placeholder="hh:mm pm" id="time" />
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div id="linksContainer" class="my-3">
                        <div id="links">
                            <div class="form-check"><input type="checkbox" name="update_posted_date" value="1" id="update_posted_date" class="form-check-input"> <label for="update_posted_date" class="form-check-label" style="font-size: 1em;">
                                    Update posted date
                                </label></div>
                            </div</div>


                            <div class="text-center">
                                <button type="submit" id ="btn" class="btn btn-secondary">@lang('general.save')</button>
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
    $(document).ready(function() {
        // Get value on button click and show alert
        $("#btn").click(function() {
            var date = $("#date").val();
            var time = $("#time").val();
            if (date.length > 0 || time.length > 0) {
                $('#date').attr("required", true);
                $('#time').attr("required", true);
            } else {
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
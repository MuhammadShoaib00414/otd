@extends('layouts.app')

@section('stylesheets')
@parent
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.min.css" />
@endsection

@section('content')
<div class="col-5 mx-auto">
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
              <form action="/posts/{{ $article->listing->id }}/content" method="post" id="form" enctype="multipart/form-data" id="form-submission">
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
                          
                            @foreach($groups as $group)
                           
                            @include('admin.posts.partials.getShareableGroups', ['group' => $group, 'checkedGroups' => $article->listing->groups])
                            @endforeach
                        </div>
                        <hr>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" name="update_date" id="update_date">
                            <label class="form-check-label" for="group55" style="font-size: 16px;">
                            Update posted date
                            </label>
                        </div>
                    <div class="text-center">
                      <button type="submit" class="btn btn-secondary">@lang('general.save')</button>
                    </div>
                </div>
            </form>
            </div>
          </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<script>
    
    $('input[name="custom_image_upload"]').change(function(e) {
        if($(this).val())
        {
            $('.image_url').addClass('d-none');
            $('#image').addClass('d-none');
        }
        else
        {
            $('.image_url').removeClass('d-none');
            $('#image').removeClass('d-none');
        }
    });
    $('#form').on('click', function (e) {
         localStorage.clear();
      });
  </script>
@endsection

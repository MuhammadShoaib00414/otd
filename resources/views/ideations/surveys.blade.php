@extends('ideations.show.layout')

@section('stylesheets')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.min.css" />
<link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">
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
    .custom__tag {
      background-color: #f1f1f1;
      padding: 0.2em 0.5em;
      border-radius: 4px;
      margin-right: 0.25em;
    }
    .custom__remove {
      font-size: 20px;
      line-height: 3px;
      position: relative;
      top: 2px;
      padding-left: 0.1em;
    }
    .custom__remove:hover {
      cursor: pointer;
    }
    .multiselect__option--highlight {
      background: #ffc6be;
      color: #000;
    }
    .multiselect__option--highlight::after {
      background: #f19b8f;
      color: #000;
    }
    .hover-hand:hover { cursor: pointer; }
    .nav-tabs .nav-item .nav-link:not(.active) {
      color: #515457;
    }
    .nav-item .nav-link.active {
      border-color: #1a2b40 !important;
      color: #1a2b40;
      font-weight: bold;
    }
</style>
@endsection

@section('inner-content')
<div class="text-right mb-2">
  <button class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#addSurveyModal"><i class="icon-plus"></i> @lang('ideations.add-survey')</button>
</div>
<div class="row">
    <div class="col-md-12">
      <div class="row justify-content-start align-items-stretch">
        @forelse($ideation->surveys as $survey)
        <div class="col-sm-4 px-2 mb-3">
            <div class="card mb-0">
                <a href="{{ $survey->url }}" target="_blank" style="display: block; padding-top: 55%; width: 100%; background-color: #eee; background-image: url('{{ $survey->image_url }}'); background-size: cover; background-position: center;"></a>
                <div class="card-body">
                    <a href="{{ $survey->url }}" target="_blank">
                        <h5 class="card-title">{{ $survey->title }}</h5>
                    </a>
                </div>
            </div>
            @if($ideation->owner->id == request()->user()->id)
              <div class="d-flex justify-content-end">
                <form action="/ideations/{{ $ideation->slug }}/surveys/{{ $survey->id }}/delete" method="post">
                  @csrf
                  @method('delete')
                  <button type="submit" class="btn btn-sm btn-light deletePostButton" style="border-top-left-radius: 0; border-top-right-radius: 0; color: #666160;">@lang('general.delete')</button>
                </form>
              </div>
            @endif
        </div>
        @empty
          <div class="col-12">
            @include('partials.empty')
          </div>
        @endforelse
      </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="addSurveyModal">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form class="modal-content" action="/ideations/{{ $ideation->slug }}/surveys" method="post">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">@lang('ideations.add-survey')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <div class="d-flex align-items-center">
              <div class="input-group" style="max-width: 500px;">
                  <input type="text" class="form-control" name="url" id="url" placeholder="URL">
                  <div class="input-group-append">
                      <button type="button" class="btn btn-secondary" id="fetchInfo">@lang('content.fetch')</button>
                  </div>
              </div>
              <div class="ml-1">
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
          <input type="hidden" name="title">
          <input type="hidden" name="image">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary-outline" data-dismiss="modal">@lang('general.close')</button>
        <button type="submit" class="btn btn-primary" id="submitButton" disabled="true">@lang('ideations.add-survey')</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
  <script>
    $('#fetchInfo').on('click', function () {
        $('.fa-spinner').removeClass('d-none');
        $.ajax({
            url: '/articles/fetch',
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
                $('#submitButton').prop('disabled', false);
            },
            error: function (response) {
                console.log(response);
            }
        });
    });
  </script>
@endsection
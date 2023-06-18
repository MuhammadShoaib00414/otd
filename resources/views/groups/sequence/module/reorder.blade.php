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
    <h3 class="font-weight-bold mb-0">Reorder Modules</h3>
  </div>
  
  <form action="/groups/{{ $group->slug }}/sequence/reorder" method="post">
    @csrf

    <div class="card">
      <div class="card-body">

        @foreach($modules as $module)
          <div class="form-row mb-3">
            <div class="col-1">
              <input type="text" value="{{ $module->order_key }}" name="modules[{{ $module->id }}][order_key]" class="form-control text-center">
            </div>
            <div class="col-10">
              <p class="mb-1 mt-1">{{ $module->name }}</p>
              <img src="{{ $module->thumbnail_image_path }}" style="max-width: 200px;">
            </div>
          </div>
        @endforeach

        <div>
          <button id="submitButton" type="submit" class="btn btn-primary">@lang('general.save')</button>
        </div>
      </div>
    </div>

  </form>
@endsection
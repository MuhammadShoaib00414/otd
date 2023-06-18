@extends('layouts.app')

@section('stylesheets')
  <style>
    .video-responsive{
      overflow:hidden;
      padding-bottom:56.25%;
      position:relative;
      height:0;
  }
  .video-responsive iframe{
      left:0;
      top:0;
      height:100%;
      width:100%;
      position:absolute;
  }
  </style>
@endsection

@section('content')

<div class="main-container bg-lightest-brand">

  <section class="pt-3">
    <div class="container-fluid">
      <div class="row justify-content-center">

        <div class="col-12 col-md-8">
          <div class="mt-4 mb-5">
              <p>
                <a href="#" onclick="window.history.go(-1); return false;">@lang('messages.back')</a>
              </p>
              <h5>{{ $post->title }}</h5>
              <div class="video-responsive">
                {!! $post->code !!}
              </div>
          </div>
        </div>
      
      </div>
    </div>
    <!--end of container-->
</section>
</div>
@endsection
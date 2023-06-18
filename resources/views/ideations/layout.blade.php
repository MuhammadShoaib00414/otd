@extends('layouts.app')

@section('content')
<div style="background-color: #565d6b;">
  <div class="container-fluid pt-2 pb-3" style="position: relative;">
    <div  style="font-size: 14px;">
      <a href="{{ route('spa') }}" class="d-inline-block mb-2" style="color: #fff;">@lang('messages.my-dashboard')</a> <i class="icon-chevron-small-right" style="color: #fff;"></i> <a href="/ideations" style="color: #fff;">@lang('ideations.ideations')</a>
    </div>
    <h4 class="mb-0 text-center no-underline" style="color: #fff;">@lang('ideations.ideations')</h4>
  </div>
</div>
<div class="main-container bg-lightest-brand">
  @yield('header-content')
  <div class="container-fluid" style="min-height: 60vh;">
    <div class="row justify-content-center">
      <div class="col-md-10">
        @yield('inner-content')
      </div>
    </div>
  </div>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div style="background-color: #565d6b;">
  <div class="container-fluid py-3" style="position: relative;">
    <a href="/home" class="d-inline-block mb-2" style="font-size: 14px; color: #fff;"><i class="icon-chevron-small-left"></i> @lang('messages.my-dashboard')</a>
    <h4 class="mb-0 text-center no-underline" style="color: #fff;">@lang('introductions.introductions')</h4>
  </div>
</div>
<div class="container-fluid">
  <main class="main" role="main">
    <div class="pb-5 pt-3 bg-lightest-brand">
      <div class="px-2">
        <div class="row">
          <div class="col-md-12">
          <div class="row">
              <div class="col-md-2">
              </div>
              <div class="col-md-9 pr-0 mr-0">
                <div class="d-flex justify-content-between mb-2">
                  <div></div>
                  <form method="GET" action="/introductions/{{ request()->is('*sent*') ? 'sent' : 'received' }}">
                    <div class="input-group">
                      <input class="form-control" name="s" value="{{ isset($_GET['s']) ? $_GET['s'] : '' }}">
                      <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">@lang('messages.search')</button>
                      </div>
                    </div>
                  </form>
                  <a href="/introductions/new" class="btn btn-secondary mr-2">@lang('general.new')</a>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-2">
                <div class="row">
                  <span @if(Request::is('*received*')) style="transform: translateY(35%); max-height: 23px; border-right: 3px solid #f29181; border-radius:0px 10px 10px 0px;"@endif></span>
                  <a class="nav-link pl-1" href="/introductions/received">@lang('introductions.received')</a>
                </div>
                <div class="row">
                  <span @if(Request::is('*sent*')) style="transform: translateY(35%); max-height: 23px; border-right: 3px solid #f29181; border-radius:0px 10px 10px 0px;"@endif></span>
                  <a class="nav-link pl-1" href="/introductions/sent">@lang('introductions.sent')</a>
                </div>
              </div>
              <div class="col-md-9">
                @yield('inner-content')
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>
@endsection
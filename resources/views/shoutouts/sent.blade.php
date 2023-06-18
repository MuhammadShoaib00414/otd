@extends('layouts.app')

@section('content')
<div class="bg-primary-600">
  <div class="container-fluid py-3" style="position: relative;">
    <a href="/home" class="d-inline-block mb-2" style="font-size: 14px; color: #fff;"><i class="icon-chevron-small-left"></i> @lang('messages.my-dashboard')</a>
    <h4 class="mb-0 text-center no-underline" style="color: #fff;">@lang('shoutouts.my_shoutouts')</h4>
  </div>
</div>
<div class="container-fluid">
  <main class="main" role="main">
    <div class="pb-5 pt-3 bg-lightest-brand">
      <div class="col-md-11 mb-2 pr-0">
        <div class="d-flex justify-content-end">
          <a href="/shoutouts/create" class="btn btn-primary btn-sm mr-1">@lang('shoutouts.new_shoutout')</a>
        </div>
      </div>
        <div class="row">
          <div class="col-md-2">
            <div class="row">
              <a class="nav-link pl-1" href="/shoutouts/received">@lang('shoutouts.received')</a>
            </div>
            <div class="row">
              <span style="transform: translateY(35%); max-height: 23px; border-right: 3px solid #f29181; border-radius:0px 10px 10px 0px;"></span>
              <a class="nav-link pl-1" href="/shoutouts/sent">@lang('shoutouts.sent')</a>
            </div>
          </div>
          <div class="col-md-9">
            <div class="card">
              <table style="table-layout: fixed;" class="table mb-0">
                <thead>
                  <tr>
                    <td>@lang('shoutouts.you_shouted')</td>
                    <td>@lang('general.message')</td>
                  </tr>
                </thead>
                <tbody>
                  @foreach($shoutouts as $shoutout)
                    @if($shoutout->shouted()->exists() && $shoutout->shouting()->exists())
                      <tr>
                        <td>
                          <a href="/users/{{ $shoutout->shouted->id }}" class="d-flex align-items-center justify-content-start">
                            <div class="mr-3" style="height: 3.5em; width: 3.5em; min-height: 3.5em; min-width: 3.5em; border-radius: 50%; background-color: #eee; background-image: url('{{ $shoutout->shouted->photo_path }}'); background-size: cover; background-position: center;">
                            </div>
                            <div>
                              {{ $shoutout->shouted->name }}<br>
                              {{ $shoutout->shouted->job_title }}
                            </div>
                          </a>
                        </td>
                        <td>{{ $shoutout->body }}</td>
                      </tr>
                    @endif
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>
@endsection
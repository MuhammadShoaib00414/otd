@extends('layouts.app')

@section('content')

  <main class="main" role="main">
    <div class="py-5 bg-lightest-brand">
      <div class="container">
        <div class="row">
          <div class="col-md-9 mx-auto">
            <div class="row">
              <h3 class="col"> @lang('introductions.edit-introduction') </h3>
              <form method="post" action="/introductions/{{ $introduction->id }}" class="mr-2">
                @csrf
                @method('delete')
                <button type="submit" class="btn btn-light col align-self-end text-muted">@lang('general.delete')</button>
              </form>
            </div>
            <div class="card">
              <div class="card-body">
                <div class="text-center mb-2 mt-2">
                  <b class="text-uppercase" style="color: #f49181;">@lang('introductions.introducing')</b>
                </div>
                <div class="d-flex align-items-center justify-content-center">
                  @foreach($introduction->users as $user)
                  <span  class="d-flex align-items-center mx-3 no-underline" style="color: #343a40;">
                    <div style="width: 4em; height: 4em; border-radius: 50%; background-image: url('{{ $user->photo_path }}'); background-size: cover; background-position: center; overflow: hidden;">
                    </div>
                    <div>
                      <div class="ml-2">
                        <b>{{ $user->name }}</b><br>
                        {{ $user->job_title }}
                      </div>
                    </div>
                  </span>
                  @endforeach
                </div>
                <hr>
                <form method="post" action="/introductions/{{ $introduction->id }}" id="app" class="px-4 pb-0 mx-auto" style="max-width: 600px;">
                  @csrf
                  @method('put')
                  <label>@lang('introductions.edit-message')</label>
                  <textarea name="message" class="form-control mx-auto" style="height: 100px;">{{ $introduction->message }}</textarea>
                  <button type="submit" class="btn btn-primary mt-2 float-right">@lang('general.submit')</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
  @endsection
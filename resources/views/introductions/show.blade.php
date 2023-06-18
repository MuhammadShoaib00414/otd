@extends('layouts.app')

@section('content')

  <main class="main" role="main">
    <div class="py-4 bg-lightest-brand">
      <div class="container">
        <div>
          @include('introductions.partials.backlinks')
        </div>
        <div class="row">
          <div class="col-md-10 col-lg-9 mx-auto">
            @if($introduction->sent_by == request()->user()->id)
              <div class="text-right mb-3">
                <a href="/introductions/{{ $introduction->id }}/edit"><button class="btn btn-primary">@lang('general.edit')</button></a>
              </div>
            @endif
            <div class="card">
              <div class="card-body">
                <div class="text-center mb-2 mt-2">
                  <b class="text-uppercase" style="color: #f49181;">@lang('introductions.introduction-by')</b>
                </div>
                <a href="/users/{{ $introduction->invitee->id }}" class="d-flex justify-content-center align-items-center mb-3 no-underline" style="color: #343a40;">
                  <div style="width: 3em; height: 3em; border-radius: 50%; background-image: url('{{ $introduction->invitee->photo_path }}'); background-size: cover; background-position: center; overflow: hidden;">
                  </div>
                  <div class="ml-2">
                    <b>{{ $introduction->invitee->name }}</b><br>
                    {{ $introduction->invitee->job_title }}
                  </div>
                </a>

                <hr>

                {!! nl2br(e($introduction->message)) !!}

                <hr>

                <div class="text-center mb-2 mt-2">
                  <b class="text-uppercase" style="color: #f49181;">@lang('introductions.introducing')</b>
                </div>

                <div class="d-flex flex-wrap align-items-center justify-content-center">
                  @foreach($introduction->users as $user)
                  <a href="/users/{{ $user->id }}"  class="d-flex align-items-center mx-3 mb-3 no-underline" style="color: #343a40; min-width: 250px;">
                    <div style="width: 4em; height: 4em; border-radius: 50%; background-image: url('{{ $user->photo_path }}'); background-size: cover; background-position: center; overflow: hidden;">
                    </div>
                    <div>
                      <div class="ml-2">
                        <b>{{ $user->name }}</b><br>
                        {{ $user->job_title }}
                      </div>
                    </div>
                  </a>
                  @endforeach
                </div>

                <div class="text-center mb-2 mt-4">
                  <a href="/messages/new?user={{ $introduction->otherUser->id }}" class="btn btn-primary">@lang('general.message') {{ $introduction->otherUser->name }}</a>
                </div>

              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </main>
  @endsection
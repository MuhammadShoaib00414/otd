@extends('layouts.app')

@section('stylesheets')
<style>
.hover-hand:hover {
  cursor: pointer;
}
.bg-selected {
  background-color: rgba(0,0,0,0.08);
}
</style>
@endsection

@section('content')
<div style="background-color: #565d6b;">
  <div class="container-fluid py-3" style="position: relative;">
    @if($backlink)
      <a href="{{ $backlink['url'] }}" class="d-inline-block mb-2" style="font-size: 14px; color: #fff;"><i class="icon-chevron-small-left"></i> {{ $backlink['text'] }}</a>
    @else
      <a href="{{ route('spa') }}" class="d-inline-block mb-2" style="font-size: 14px; color: #fff;"><i class="icon-chevron-small-left"></i> @lang('messages.my-dashboard')</a>
    @endif
    <h4 class="mb-2 text-center no-underline" style="color: #fff;">{{ getsetting('find_your_people_alias') }}</h4>
    @if(isset($description))
    <div class="row">
      <div class="col-md-8 mx-md-auto">
        <span style="color:white; font-size: 0.85em;">{{ $description }}</span>
      </div>
    </div>
    @endif
  </div>
</div>

<div class="container-fluid" style="min-height: 72vh;">
  <div class="row">
    <div class="col-md-4 py-3">
      @foreach($taxonomies as $taxonomy)
        <div class="card card-body">
          <a href="#taxonomy{{ $taxonomy->id }}" data-toggle="collapse" class="mb-1 my-md-auto" style="color: {{ getThemeColors()->accent['300'] }}; font-size:1.6em; text-decoration: none;"><b>{{ $taxonomy->name }}</b></a>
          <div id="taxonomy{{ $taxonomy->id }}" class="collapse {{ (request()->has('options') && $taxonomy->options_with_users->only(request()->options)->count()) ? 'show' : '' }}">
          @foreach($taxonomy->list as $groupName => $groups)
              <div class="mb-3">
                  <p class="font-weight-bold mb-0">{{ $groupName }}</p>
                  @foreach($groups as $option)
                      <a href="/browse?{{ $option->query }}" class="mr-2{{ (request()->has('options') && in_array($option->id, request()->options)) ? ' font-weight-bold bg-selected' : '' }}">{{ $option->name }}</a>
                  @endforeach
              </div>
          @endforeach
          </div>
        </div>
      @endforeach
    </div>
    <div class="col-md-8" id="results">
      @if(count($results) > 0)
      <p class="my-3">{{ count($results) }} @lang('browse.results')</p>
      @endif
      <div class="row justify-content-center align-items-stretch my-3">
      @forelse($results as $result)
        <div class="col-md-4 mb-3">
          <a href="/users/{{ $result->id }}?back=Find Your People" class="card no-underline h-100">
            <div class="card-body d-flex align-items-center justify-content-center">
              <div class="d-flex flex-column align-items-center justify-content-center">
                <div class="mb-2" style="height: 5.5em; width: 5.5em; border-radius: 50%; background-image: url('{{ $result->photo_path }}'); background-size: cover; background-position: center;">
                </div>
                <div class="pt-1 text-center">
                  <span class="d-block" style="color: #343a40; font-weight: 600;">{{ $result->name }}</span>
                  <span class="d-block card-subtitle my-1 text-muted">{{ $result->job_title }}</span>
                  <span class="d-block mt-1 text-muted">{{ $result->company }}</span>
                </div>
              </div>
            </div>
          </a>
        </div>
      @empty
      <div class="bg-white w-100 p-y5">
        <p class="m-4 ">
          @if(request()->has('options'))
            @lang('browse.empty')
          @elseif($taxonomies->count())
            @lang('browse.prompt')
          @else
            @lang('browse.not-enough-members')
          @endif
        </p>
      @endforelse
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
  @if(request()->has('options'))
    <script>
      $(document).ready(function () {
        if ($(window).width() < 680) {
          $('html, body').animate({
              scrollTop: $("#results").offset().top
          }, 600);
        }
      })
    </script>
  @endif
@endsection

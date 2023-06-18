@extends('layouts.app')

@section('stylesheets')
<style>
    .group-header-bg {
      background-color: #565d6b;
    }
</style>
@endsection

@section('content')
<div class="group-header-bg py-3">
  <div class="container-fluid pb-2" style="position: relative;">
    <div class="mt-1" style="font-size: 14px;">
      <div class="d-none d-lg-block d-md-block">
          @include('groups.partials.backlinks', ['group' => $group])
        </div>
    </div>
  </div>
  <h4 class="text-center text-white py-3">{{ $group->subgroups_page_name }}</h4>
  @if($group->description)
  <div  style="max-width:50%" class="mx-md-auto pb-2">
    <div class="text-center{{ ($group->header_bg_image_path) ? ' d-none' : '' }}">
      <p style="font-size:0.8em; color:white;">{{ $group->description }}</p>
    </div>
  </div>
  @endif
</div>
<div class="main-container bg-lightest-brand py-4">
  <div class="container">
    <div class="row align-items-stetch">
      @foreach($subgroups as $subgroup)
        @if($subgroup->doesUserHaveAccess(request()->user()->id))
          <div class="col-md-4 mb-3">
            <a href="/groups/{{ $group->slug }}/subgroups/{{ $subgroup->slug }}/log" class="card h-100">
              <div style="background-color: #eee; background-image: url('{{ $subgroup->thumbnail_image_url }}'); background-size: cover; background-position: center;">
                <div style="width: 100%; margin-top: 51%;"></div>
              </div>
              <div class="card-body text-center font-weight-bold d-flex flex-row justify-content-center align-items-center">
                <span>{{ $subgroup->name }}</span>
              </div>
            </a>
          </div>
        @endif
      @endforeach
    </div>
  </div>
</div>
@endsection


@extends('layouts.app')
<?php 
    use App\Group;
    $slug = Request::segment(2);
    $groups = Group::where('slug', '=', $slug)->first();
?>

@section('stylesheets')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.min.css" />
<link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css">
<style>
  @media (min-width: 576px) {
    .collapse.dont-collapse-sm {
      display: block!important;
      height: auto !important;
      visibility: visible;
    }
  }
  #groupMenuCaret {
    -webkit-transform: translateY(2px);
    -moz-transform: translateY(2px);
    transform: translateY(2px);
  }

  .btn[aria-expanded=false] i:before {
    font-weight: 900;
    content: "\f107";
    float: right;
    transition: all .4s;
    -webkit-transform: rotate(-90deg);
    -moz-transform: rotate(-90deg);
    transform: rotate(-90deg);
  }

  .btn[aria-expanded=true] i:before {
    font-weight: 900;
    content: "\f107";
    float: right;
    transition: all .4s;
  }
  .group-header-bg {
    background-color: {{ getsetting('group_header_color') }};
  }
</style>
@endsection

@section('content')
@section('header-content')
  <div class="group-header-bg">
    <div class="container-fluid pt-2 pb-3" style="position: relative;">
        <h4 class="mb-0 mt-4 text-center no-underline" style="color: #fff;"> {{$groups->name }}</h4>
    </div>
    
    @if($groups->description)
    <div style="max-width:50%" class="mx-md-auto pb-2">
      <div class="text-center{{ ($group->header_bg_image_path) ? ' d-none' : '' }}">
        <p style="font-size:0.8em; color:white;">{{ $groups->description }}</p>
      </div>
    </div>
    @endif
  </div>
@show
<div class="main-container bg-lightest-brand py-4">
  <div class="container-fluid">
    <div class="row">
   
      @if($group->has_home_image)
        <div class="d-none d-lg-block d-md-block">
          @include('groups.partials.backlinks', ['group' => $groups])
        </div>
      @endif
      <div class="col-sm-3 mt-2">
        <div class="d-flex justify-content-around mb-3 d-lg-none d-xl-none d-md-none d-sm-none">
          <button class="btn btn-light text-center" style="background-color: #fff; width: 100%; border: 1px solid;" type="button" data-toggle="collapse" data-target="#groupMenu" aria-expanded="false" aria-controls="groupMenu">
            <i style="overflow: hidden;" id="groupMenuCaret" class="fas fa-caret-down mx-1"></i> {{ strlen($group->name) > 35 ? substr($group->name, 0, 35).'...' : $group->name }} Menu
          </button>
        </div>
        <div class="collapse dont-collapse-sm" id="groupMenu">
          <div class="well">
            
            @include('groups.partials.menu')
            @if($group->twitter_handle || $group->facebook_url || $group->instagram_handle || $group->linkedin_url || $group->website_url)
              <div class="text-center">
                  @if($group->twitter_handle)
                  <a href="https://twitter.com/{{ $group->twitter_handle }}" target="_blank" class="social-icon-circle mr-1" style="background-color: {{ getThemeColors()->accent['300'] }}"><i class="socicon-twitter"></i></a>
                  @endif
                  @if($group->facebook_url)
                  <a href="{{ $group->facebook_url }}" target="_blank" class="social-icon-circle mr-1" style="background-color: {{ getThemeColors()->accent['300'] }}"><i class="socicon-facebook"></i></a>
                  @endif
                  @if($group->instagram_handle)
                  <a href="https://www.instagram.com/{{ $group->instagram_handle }}" target="_blank" class="social-icon-circle mr-1" style="background-color: {{ getThemeColors()->accent['300'] }}"><i class="socicon-instagram"></i></a>
                  @endif
                  @if($group->linkedin_url)
                  <a href="{{ $group->linkedin_url }}" target="_blank" class="social-icon-circle mr-1" style="background-color: {{ getThemeColors()->accent['300'] }}"><i class="socicon-linkedin"></i></a>
                  @endif
                  @if($group->website_url)
                  <a href="{{ (str_contains($group->website_url, '//')) ? $group->website_url : 'http://'.$group->website_url }}" target="_blank" class="social-icon-circle" style="background-color: {{ getThemeColors()->accent['300'] }}"><i class="icon-link"></i></a>
                  @endif
              </div>
            @endif
            @if($group->admins()->count())
              <div class="bg-light-brand p-3 mt-3 mb-2">
                <p id="banner_cta_title" class="font-weight-bold">{{ $group->banner_cta_title }}</p>
                <p id="banner_cta_paragraph">{{ $group->banner_cta_paragraph }}</p>
                <a id="banner_cta_button" href="/messages/new?{{ http_build_query($group->banner_cta_users) }}" class="btn btn-light px-0" style="white-space: normal; max-width: 100%; overflow: hidden;">{{ $group->banner_cta_button }} <i class="icon-controller-play ml-1"></i></a>
              </div>
            @endif
            <p><b>@lang('groups.Group Admins')</b></p>
            <div class="d-flex flex-row flex-wrap justify-content-start">
              @foreach($group->admins as $admin)
                <a href="/users/{{ $admin->id }}" class="d-block mr-2 mb-2" data-toggle="tooltip" title="{{ $admin->name }}" style="width: 3em; height: 3em; border-radius: 50%; background-color: #fff; background-image: url('{{ $admin->photo_path }}'); background-size: cover; background-position: center; overflow: hidden;"></a>
              @endforeach
            </div>
          </div>
        </div>

        @if(Request::path() == 'groups/' . $group->slug)
        <div class="pt-3 d-none d-sm-block">
          <p class="font-weight-bold">@lang('groups.Latest articles')</p>
          <div id="latestArticles">
            @foreach($twoWeekOldArticles as $article)
              <div class="mb-3">
                <a href="{{ $article->post->url }}" data-redirect="/article/{{ $article->post->id }}" class="no-underline" style="background-color: #f7d0c9; background-image: url('{{ $article->post->image_url }}'); background-size: cover; background-position: center; height: 12em; display: flex; align-items: flex-end;" target="_blank">
                  <span class="d-block px-2 py-1 mb-2" style="background-color: #fff; width: 85%; color: #1e1e20; border-left: 5px solid {{ getThemeColors()->accent['300'] }}; transform: translateX(-5px);">
                  {{ $article->post->title }}
                </span>
              </a>
            </div>
            @endforeach
          </div>
        </div>
        @endif
      </div>
      <div class="@yield('body-class', 'col-md-6') pt-2">
        @yield('inner-content')
      </div>
    </div>
  </div>
</div>
@endsection

<!-- This show folder is a step toward organizing Ideation views. But I digress. We have enough bugs as it is. -->
@extends('ideations.layout')

@section('header-content')
<div class="mb-3" style="background-color: #fff; border-bottom: 1px solid #eaecf0;">
    <div class="container-fluid pt-4">
      <div class="row justify-content-center">
        <div class="col-md-10">
          <div class="d-flex justify-content-between mb-2">
            <h4 class="mb-0">{{ $ideation->name }} <small class="ml-1" style="font-size: 0.7em;">
              @if($ideation->owner->id == request()->user()->id || request()->user()->is_admin)
                <a href="/ideations/{{ $ideation->slug }}/edit" dusk="edit-ideation">@lang('general.edit')</a>
              @endif
            </small></h4>
            <div class="d-flex justify-content-end">
              @if($ideation->owner->id == request()->user()->id || request()->user()->is_admin)
                <form method="post" action="/ideations/{{ $ideation->slug }}/delete" class="d-block w-100 mr-1" >
                    @method('delete')
                    @csrf
                    <button type="submit" dusk="delete-ideation" class="d-block w-100 mb-2 btn btn-grey" id="deleteButton"><i class="icon-trash"></i></button>
                </form>
              @endif
            </div>
          </div>
          <ul class="nav nav-tabs">
            <li class="nav-item">
              <a class="nav-link {{ request()->is('*'.$ideation->slug) ? 'active' : '' }}" href="/ideations/{{ $ideation->slug }}">@lang('general.discussion')</a>
            </li>
            @if($ideation->is_current_user_participant)
              <li class="nav-item">
                <a class="nav-link {{ request()->is('*files') ? 'active' : '' }}" href="/ideations/{{ $ideation->slug }}/files">@lang('general.files') @if($ideation->files()->count())<span class="badge badge-light">{{ $ideation->files()->count() }}</span>@endif</a>
              </li>
            @endif
            <li class="nav-item">
              <a class="nav-link {{ request()->is('*members') ? 'active' : '' }}" href="/ideations/{{ $ideation->slug }}/members">@lang('general.members')</a>
            </li>
            @if($ideation->is_current_user_participant)
              <li class="nav-item">
                <a class="nav-link {{ request()->is('*articles') ? 'active' : '' }}" href="/ideations/{{ $ideation->slug }}/articles">@lang('general.content')</a>
              </li>
              <li class="nav-item">
                <a class="nav-link {{ request()->is('*surveys') ? 'active' : '' }}" href="/ideations/{{ $ideation->slug }}/surveys">@lang('ideations.surveys')</a>
              </li>
            @endif
            <li class="nav-item">
              <a class="nav-link {{ request()->is('*video') ? 'active' : '' }}" href="/ideations/{{ $ideation->slug }}/video">@lang('ideations.video-confrence')</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
</div>
@endsection
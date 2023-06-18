@extends('groups.layout')

@section('body-class', 'col-md-9')

@section('inner-content')
  <div class="d-flex justify-content-between align-items-center mb-2">
    <h3 class="font-weight-bold mb-0">{{ $group->sequence->name }}</h3>
    @if($authUser->is_admin || $group->isUserAdmin($authUser))
    <div class="dropdown">
      <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Manage
      </button>
      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
        <a class="dropdown-item" href="/groups/{{ $group->slug }}/sequence/new">New Module</a>
        <a class="dropdown-item" href="/groups/{{ $group->slug }}/sequence/reorder">Reorder Modules</a>
        <a class="dropdown-item" href="/groups/{{ $group->slug }}/sequence/reminders">Reminders</a>
        <a class="dropdown-item" href="/groups/{{ $group->slug }}/edit#sequence">Settings</a>
      </div>
    </div>
    @endif
  </div>
  <div class="row">
    @foreach($modules as $module)
      <a {{ ($module->is_available || ($authUser->is_admin || $group->isUserAdmin($authUser))) ? 'href=/groups/'.$group->slug.'/sequence/modules/'.$module->id : '' }} class="d-block col-12 col-sm-6 col-md-4 mb-3">
        <div style="background-image: url('{{ $module->thumbnail_image_path }}'); background-repeat: no-repeat; background-size: contain; background-position: center; width: 100%; {{ (!$module->is_available) ? 'filter: grayscale(1);' : '' }}{{ ($module->hasUserCompleted($authUser)) ? ' border: 3px solid #26c126;' : '' }}">
          <div style="padding-top: 60%; width: 100%;"></div>
          @if($module->hasUserCompleted($authUser))
            <i class="fas fa-check-circle" style="color: #26c126; position: absolute; bottom: 1rem; right: 2rem; font-size: 3em;"></i>
          @endif
        </div>
      </a>
    @endforeach

  </div>
@endsection

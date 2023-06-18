@extends('groups.layout')

@section('stylesheets')
  @parent
  <style>
    .module-content figure img {
      max-width: 100%;
    }
  </style>
@endsection

@section('inner-content')
  <div class="d-flex justify-content-between align-items-center mb-2">
    <h3 class="font-weight-bold mb-0">{{ $module->name }}</h3>
    @if($authUser->is_admin || $group->isUserAdmin($authUser))
      <a href="/groups/{{ $group->slug }}/sequence/modules/{{ $module->id }}/edit" class="btn btn-sm btn-outline-secondary">Edit</a>
    @endif
  </div>

  <img src="{{ $module->thumbnail_image_path }}" style="width: 100%; margin-bottom: 1em;">

  <div class="card">
    <div class="card-body module-content">
      {!! str_replace(['"//www.youtube.com', '"https://www.youtube.com'], '"https://youtube.com', $module->content) !!}
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      @if($moduleUser->completed_at)
        @if($module->next_module)
          <p class="text-center">You've completed this module!</p>
          <div class="text-center">
            <a href="/groups/{{ $group->slug }}/sequence/modules/{{ $module->next_module->id }}" class="btn btn-primary">Next <i class="fas fa-angle-right"></i></a>
          </div>
        @else
          <p class="text-center">Congrats! You've finished all the modules!</p>
        @endif
        <form action="/groups/{{ $group->slug }}/sequence/modules/{{ $module->id }}/uncomplete" method="post" class="d-flex justify-content-center align-items-center">
          @csrf
          <button type="submit" onclick="return confirm('Are you sure you want to mark this as incomplete?')" class="btn btn-outline-primary mx-auto">Mark as Incomplete</button>
        </form>
      @else
        <form action="/groups/{{ $group->slug }}/sequence/modules/{{ $module->id }}/completed" method="post">
          @csrf
          <button type="submit" onclick="return confirm('Are you sure you want to mark this as completed?')" class="btn btn-primary w-100">Mark as Completed</button>
        </form>
      @endif
    </div>
  </div>

@endsection

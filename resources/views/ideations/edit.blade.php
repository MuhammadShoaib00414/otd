@extends('ideations.layout')

@section('inner-content')
    <div class="d-flex my-3 justify-content-between align-items-center">
        <h4 class="mb-0">@lang('ideations.edit')</h4>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger">
          <ul>
              @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
              @endforeach
          </ul>
        </div>
    @endif
    @if(session()->has('participants-error'))
        <div class="alert alert-danger">
            {{ session('participants-error') }}
        </div>
    @endif
    <div class="card">
        <div class="card-body">
            <form action="/ideations/{{ $ideation->slug }}" method="post">
                @csrf
                @method('put')
                <div class="form-group">
                    <label for="title">@lang('ideations.title-prompt')</label>
                    <input type="text" name="name" dusk="ideation-title" class="form-control" value="{{ $ideation->name }}" required maxlength="75">
                </div>
                <hr>
                <div class="form-group mb-4">
                    <label for="max_participants">@lang('ideations.limit-participants')</label>
                    <input type="text" name="max_participants" id="max_participants" class="form-control" value="{{ $ideation->max_participants }}" style="max-width: 80px;">
                    <span class="small text-muted">@lang('ideations.limit-participants-description')</span>
                </div>
                @if(!$ideation->has_max_participants)
                <div class="form-group">
                    <label for="title">@lang('ideations.groups-prompt')</label>
                    @foreach($groups as $group)
                      <div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" value="{{ $group->id }}" name="groups[]" id="group{{ $group->id }}" {{ $ideation->groups->contains($group->id) ? 'checked' : '' }}>
                        <label class="form-check-label" for="group{{ $group->id }}" style="font-size: 16px;">
                          {{ $group->name }}
                        </label>
                      </div>
                      @foreach($group->subgroups as $subgroup)
                        <div class="form-check mb-1 ml-2">
                          <input class="form-check-input" type="checkbox" value="{{ $subgroup->id }}" name="groups[]" id="group{{ $subgroup->id }}" {{ $ideation->groups->contains($subgroup->id) ? 'checked' : '' }}>
                          <label class="form-check-label" for="group{{ $subgroup->id }}" style="font-size: 16px;">
                            {{ $subgroup->name }}
                          </label>
                        </div>
                        @foreach($subgroup->subgroups as $thirdsubgroup)
                            <div class="form-check mb-1 ml-3">
                              <input class="form-check-input" type="checkbox" value="{{ $thirdsubgroup->id }}" name="groups[]" id="group{{ $thirdsubgroup->id }}" {{ $ideation->groups->contains($thirdsubgroup->id) ? 'checked' : '' }}>
                              <label class="form-check-label" for="group{{ $thirdsubgroup->id }}" style="font-size: 16px;">
                                {{ $thirdsubgroup->name }}
                              </label>
                            </div>
                          @endforeach
                      @endforeach
                    @endforeach
                </div>
                </div>
                @endif
                <div class="text-right">
                    <button type="submit" dusk="submit" class="btn btn-secondary">@lang('ideations.save-and-post')</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@extends('ideations.layout')

@section('inner-content')
    <div class="d-flex my-3 justify-content-between align-items-center">
        <h4 class="mb-0">@lang('ideations.approve-ideation')</h4>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="/ideations/{{ $ideation->slug }}/approve" method="post">
                @csrf
                <div class="form-group">
                    <label for="title">@lang('ideations.title-prompt')</label>
                    <input type="text" name="name" class="form-control" value="{{ $ideation->name }}" required>
                </div>
                <hr>
                <div class="form-group mb-4">
                    <label for="max_participants">@lang('ideations.limit-participants')</label>
                    <input type="text" name="max_participants" id="max_participants" class="form-control" style="max-width: 80px;" value="{{ $ideation->max_participants }}">
                    <span class="small text-muted">@lang('ideations.limit-participants-description')</span>
                </div>
                <div class="form-group">
                    <label for="title">@lang('ideations.groups-prompt')</label>

                    @foreach($groups as $group)
                        @include('partials.groupcheckbox', ['group' => $group, 'checked' => $ideation->groups->contains($group->id), 'checkedGroups' => $ideation->groups])
                    @endforeach


                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-secondary">@lang('ideations.approve-and-post')</button>
                </div>
            </form>
        </div>
    </div>
@endsection
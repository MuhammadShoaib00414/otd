@extends('admin.ideations.show.layout')

@section('inner-page-content')
    <div class="d-flex my-3 justify-content-between align-items-center">
        <h4 class="mb-0">Edit Ideation</h4>
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
    <div class="card">
        <div class="card-body">
            <form action="/admin/ideations/{{ $ideation->id }}" method="post">
                @csrf
                @method('put')
                <div class="form-group">
                    <label for="title">Title/Topic</label>
                    <input type="text" name="name" dusk="ideation-title" class="form-control" value="{{ $ideation->name }}" required>
                </div>
                <hr>
                <div class="form-group mb-4">
                    <label for="max_participants">Limit to number of participants</label>
                    <input type="text" name="max_participants" id="max_participants" class="form-control" value="{{ $ideation->max_participants }}" style="max-width: 80px;">
                    <span class="small text-muted">Leave blank for no limit.</span>
                </div>
                <div class="form-group">
                    <label for="title">Groups allowed to participate</label>

                    @foreach($groups as $group)
                      @include('admin.ideations.partials.groupcheckbox', ['group' => $group, 'ideation' => $ideation, 'checked' => $ideation->groups->contains($group->id)])
                    @endforeach
                </div>
                <div class="text-right">
                    <button type="submit" dusk="submit" class="btn btn-secondary">@lang('general.save') & post</button>
                </div>
            </form>
        </div>
    </div>
@endsection
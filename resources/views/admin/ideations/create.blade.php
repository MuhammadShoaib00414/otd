@extends('admin.layout')

@section('page-content')
    <div class="d-flex my-3 justify-content-between align-items-center">
        <h4 class="mb-0">New Ideation</h4>
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
            <form action="/admin/ideations" method="post">
                @csrf
                <div class="form-group">
                    <label for="title">Title/Topic</label>
                    <input type="text" name="name" dusk="ideation-title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="title">Details</label>
                    <textarea class="form-control" required rows="4" dusk="ideation-body" name="body"></textarea>
                </div>
                <hr>
                <div class="form-group mb-4">
                    <label for="max_participants">Limit to number of participants</label>
                    <input type="text" name="max_participants" id="max_participants" class="form-control" style="max-width: 80px;">
                    <span class="small text-muted">Leave blank for no limit.</span>
                </div>
                <div class="form-group">
                    <label for="title">Groups allowed to participate</label>
                    @foreach($groups as $group)
                      @include('admin.ideations.partials.groupcheckbox', ['group' => $group, 'ideation' => null, 'checked' => false])
                    @endforeach
                </div>
                <div class="text-right">
                    <button type="submit" dusk="submit" class="btn btn-secondary">@lang('general.save') & post</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@extends('ideations.layout')

@section('inner-content')
    <div class="d-flex my-3 justify-content-between align-items-center">
        <h4 class="mb-0">@lang('ideations.propose-new')</h4>
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
            <form action="/ideations/propose" method="post">
                @csrf
                <div class="form-group">
                    <label for="title">@lang('ideations.title-prompt')</label>
                    <input type="text" name="name" class="form-control" required maxlength="150">
                </div>
                <div class="form-group">
                    <label for="title">@lang('general.details')</label>
                    <textarea class="form-control" required rows="4" name="body"></textarea>
                </div>
                <hr>
                <div class="form-group">
                    <label for="title">@lang('ideations.groups-prompt')</label>
                    @foreach($groups as $group)
                      <div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" value="{{ $group->id }}" name="groups[]" id="group{{ $group->id }}">
                        <label class="form-check-label" for="group{{ $group->id }}" style="font-size: 16px;">
                          {{ $group->name }}
                        </label>
                      </div>
                    @endforeach
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-secondary">@lang('ideations.submit-and-propose')</button>
                </div>
            </form>
        </div>
    </div>
@endsection
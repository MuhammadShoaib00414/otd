@extends('groups.layout')

@section('inner-content')

    <div class="card">
        <div class="card-body">
            <form action="/groups/{{ $group->slug }}/discussions/{{ $discussion->slug }}" method="post">
                @csrf
                @method('put')
                <div class="form-group">
                    <label for="name">@lang('discussions.Discussion thread name')</label>
                    <input type="text" name="name" value="{{ $discussion->name }}" class="form-control" maxlength="250">
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-secondary">@lang('general.save_changes')</button>
                </div>
            </form>
        </div>
    </div>

@endsection
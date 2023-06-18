@extends('admin.layout')

@section('page-content')
    <a href="/admin/content/feeds" class="d-inline-block mb-3"><i class="fas fa-angle-left"></i> All Feeds</a>
    
    <div class="d-flex justify-content-between">
        <h5>Add Feed</h5>
    </div>
    
    <form action="/admin/content/feeds" method="post" style="max-width: 500px;">
        @csrf
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" class="form-control">
        </div>
        <div class="form-group">
            <label for="url">Feed URL</label>
            <input type="text" id="url" name="url" class="form-control">
        </div>
        <div class="form-group">
            <label for="name">Type</label>
            <select class="custom-select" name="type">
                <option value="json">JSON</option>
                <option value="xml" disabled>XML</option>
            </select>
        </div>
        <div class="form-group mt-4">
                <label for="groups[]"><b>Groups this feed should automatically add to:</b></label>
                @foreach(App\Group::orderBy('name', 'asc')->get() as $group)
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="{{ $group->id }}" name="groups[]" id="group{{ $group->id }}">
                    <label class="form-check-label" for="group{{ $group->id }}" style="font-size: 16px;">
                      {{ $group->name }}
                    </label>
                  </div>
                @endforeach
            </div>
        <button type="submit" class="btn btn-primary">@lang('general.save') new feed</button>
    </form>
@endsection
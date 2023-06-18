@extends('admin.layout')

@section('page-content')
  <a href="/admin/content/feeds" class="d-inline-block mb-3"><i class="fas fa-angle-left"></i> All Feeds</a>

    <div class="d-flex justify-content-between">
        <h5>Edit Feed</h5>
    </div>
    
    <form action="/admin/content/feeds/{{ $feed->id }}" method="post" style="max-width: 500px;">
        @csrf
        @method('put')
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ $feed->name }}">
        </div>
        <div class="form-group">
            <label for="url">Feed URL</label>
            <input type="text" id="url" name="url" class="form-control" value="{{ $feed->url }}">
        </div>
        <div class="form-group">
            <label for="name">Type</label>
            <select class="custom-select" name="type">
                <option value="json"{{ ($feed->type == 'json') ? ' selected' : '' }}>JSON</option>
                <option value="xml"{{ ($feed->type == 'xml') ? ' selected' : '' }} disabled>XML</option>
            </select>
        </div>
        <div class="form-group mt-4">
                <label for="groups[]"><b>Groups this feed automatically adds to:</b></label>
                @foreach(App\Group::orderBy('name', 'asc')->get() as $group)
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="{{ $group->id }}" name="groups[]" id="group{{ $group->id }}"{{ ($feed->groups->contains($group->id)) ? ' checked' : '' }}>
                    <label class="form-check-label" for="group{{ $group->id }}" style="font-size: 16px;">
                      {{ $group->name }}
                    </label>
                  </div>
                @endforeach
            </div>
        <button type="submit" class="btn btn-primary">@lang('general.save') changes</button>
    </form>
    <hr>
    <form action="/admin/content/feeds/{{ $feed->id }}" method="post">
      @method('delete')
      @csrf
      <button type="submit" class="btn btn-sm btn-light mr-2" id="deleteEvent">Delete feed</button>
    </form>
@endsection

@section('scripts')
  <script>
    $('#deleteEvent').on('click', function(event) {
      event.preventDefault();
      if (confirm('Delete this feed?'))
        $('#deleteEvent').parent().submit();
    });
  </script>
@endsection
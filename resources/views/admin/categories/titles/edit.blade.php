@extends('admin.categories.layout')

@section('inner-page-content')
  <h5>Edit Title</h5>
  @if ($errors->any())
      <div class="alert alert-danger">
          <ul>
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
  @endif
  <form method="post" action="/admin/categories/titles/{{ $title->id }}">
      @csrf
      @method('PUT')
      <div class="form-group mb-2">
          <label class="form-check-label" for="enabled">Title name</label>
          <input type="text" name="name" class="form-control" style="max-width: 450px;" value="{{ $title->name }}" required>
      </div>
      <button type="submit" class="btn btn-info">@lang('general.save') changes</button>
  </form>

  <hr class="my-4">

  <form action="/admin/categories/titles/{{ $title->id }}" method="post">
      @method('delete')
      @csrf
      <button type="submit" class="btn btn-sm btn-light mr-2" id="deleteEvent">Delete title</button>
  </form>
@endsection

@section('scripts')
  <script>
    $('#deleteEvent').on('click', function(event) {
      event.preventDefault();
      if (confirm('Delete this title?'))
        $('#deleteEvent').parent().submit();
    });
  </script>
@endsection
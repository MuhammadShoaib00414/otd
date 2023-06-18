@extends('admin.categories.layout')

@section('inner-page-content')
  <h5>Edit Expense Category</h5>
  @if ($errors->any())
      <div class="alert alert-danger">
          <ul>
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
  @endif
  <form method="post" action="/admin/categories/expense-categories/{{ $category->id }}">
      @csrf
      @method('PUT')
      <div class="form-group mb-2">
          <label class="form-check-label" for="enabled">Category name</label>
          <input type="text" name="name" class="form-control" style="max-width: 450px;" value="{{ $category->name }}" required>
      </div>
      <button type="submit" class="btn btn-info mt-2">@lang('general.save') changes</button>
  </form>

  <hr class="my-4">

  <form action="/admin/categories/expense-categories/{{ $category->id }}" method="post">
      @method('delete')
      @csrf
      <button type="submit" class="btn btn-sm btn-light mr-2" id="deleteEvent">Delete expense category</button>
  </form>

@endsection

@section('scripts')
  <script>
    $('#deleteEvent').on('click', function(event) {
      event.preventDefault();
      if (confirm('Delete this category?'))
        $('#deleteEvent').parent().submit();
    });
  </script>
@endsection
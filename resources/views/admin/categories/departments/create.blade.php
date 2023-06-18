@extends('admin.categories.layout')

@section('inner-page-content')
  <h5>Add Department</h5>
  @if ($errors->any())
      <div class="alert alert-danger">
          <ul>
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
  @endif
  <form method="post" action="/admin/categories/departments">
      @csrf
      <div class="form-group mb-2">
          <label class="form-check-label" for="enabled">Department name</label>
          <input type="text" name="name" class="form-control" style="max-width: 450px;" required>
      </div>
      <button type="submit" class="btn btn-info">Create department</button>
  </form>
@endsection
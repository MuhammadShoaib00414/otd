@extends('admin.categories.layout')

@section('inner-page-content')
    <div class="d-flex justify-content-between">
        <h5>Departments</h5>
        <div class="text-right">
            <a class="btn btn-primary btn-sm" href="/admin/categories/departments/create">
              Add Department
            </a>
        </div>
    </div>
    
    <table class="table mt-2">
        <thead>
            <tr>
                <th scope="col"><b>Name</b></th>
                <th scope="col"><b>Users</b></th>
                <th></th>
            </tr>
        </thead>
        @foreach($departments as $department)
        <tr>
            <td>{{ $department->name }}</td>
            <td>{{ $department->users()->count() }}</td>
            <td class="text-right"><a href="/admin/categories/departments/{{ $department->id }}/edit">Edit</a></td>
        </tr>
        @endforeach
    </table>
@endsection
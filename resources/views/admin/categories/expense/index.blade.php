@extends('admin.categories.layout')

@section('inner-page-content')
    <div class="d-flex justify-content-between">
        <h5>Expense Categories</h5>
        <div class="text-right">
            <a class="btn btn-primary btn-sm" href="/admin/categories/expense-categories/create">
              Add Category
            </a>
        </div>
    </div>
    
    <table class="table mt-2">
        <thead>
            <tr>
                <th scope="col"><b>Name</b></th>
                <th></th>
            </tr>
        </thead>
        @foreach($categories as $category)
        <tr>
            <td>{{ $category->name }}</td>
            <td class="text-right"><a href="/admin/categories/expense-categories/{{ $category->id }}/edit">Edit</a></td>
        </tr>
        @endforeach
    </table>
@endsection
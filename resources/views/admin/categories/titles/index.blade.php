@extends('admin.categories.layout')

@section('inner-page-content')
    <div class="d-flex justify-content-between">
        <h5>Management Chain</h5>
        <div class="text-right">
            <a class="btn btn-primary btn-sm" href="/admin/categories/titles/create">
              Add Title
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
        @foreach($titles as $title)
        <tr>
            <td>{{ $title->name }}</td>
            <td class="text-right"><a href="/admin/categories/titles/{{ $title->id }}/edit">Edit</a></td>
        </tr>
        @endforeach
    </table>
@endsection
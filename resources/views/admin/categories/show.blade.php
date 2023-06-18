@extends('admin.categories.layout')

@section('inner-page-content')
    <div class="d-flex justify-content-between">
        <div class="row ml-2">
            <h5 class="mr-3 mt-1">{{ $taxonomy->name }}</h5>
            <form method="GET" action="/admin/categories/{{ $taxonomy->id }}">
                <select name="userCreated" class="custom-select custom-select-sm" onchange="this.form.submit()">
                    <option value="false" {{ (!isset($_GET['userCreated']) || $_GET['userCreated'] == 'false') ? 'selected' : ''}}>All</option>
                    <option value="true" {{ (isset($_GET['userCreated']) && $_GET['userCreated'] == 'true') ? 'selected' : '' }}>Show only user created</option>
                </select>
            </form>
        </div>
        <div class="d-flex justify-content-end align-items-center">
            <div class="dropdown">
              <a class="mr-2 btn btn-secondary btn-sm dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Options
              </a>
              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                <a class="dropdown-item" href="/admin/categories/merge?taxonomy={{ $taxonomy->id }}">Merge tool</a>
                <a class="dropdown-item" href="/admin/categories/{{ $taxonomy->id }}/edit">Edit this category</a>
                <a class="dropdown-item" href="/admin/categories/{{ $taxonomy->id }}/groupings">Bulk-edit groupings</a>
                <a class="dropdown-item" href="/admin/categories/{{ $taxonomy->id }}/add-users">Add users</a>
                <a class="dropdown-item" href="/admin/categories/{{ $taxonomy->id }}/sort">Sort</a>
                <a class="dropdown-item" href="/admin/categories/{{ $taxonomy->id }}/custom-group-sort">Sort-custom group</a>
              </div>
            </div>
            <a class="btn btn-primary btn-sm" href="/admin/options/create?taxonomy={{ $taxonomy->id }}">
              Add {{ singular($taxonomy->name) }}
            </a>
        </div>
    </div>

    <table class="table mt-2">
        <thead>
            <tr>
                <th scope="col"><b>Name</b></th>
                <th scope="col"><b>Grouping</b></th>
                <th scope="col"><b>Members</b></th>
                <th></th>
            </tr>
        </thead>
        @foreach($options as $option)
        <tr>
            <td>{{ $option->name }}</td>
            <td>{{ $option->parent }}</td>
            <td>{{ $option->users()->count() }}</td>
            <td class="text-right"><a href="/admin/options/{{ $option->id }}/edit">Edit</a></td>
        </tr>
        @endforeach
    </table>
@endsection
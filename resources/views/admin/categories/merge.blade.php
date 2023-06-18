@extends('admin.categories.layout')

@section('inner-page-content')
    <div class="col-md-8">
        <div class="d-flex justify-content-between">
            <h2>
                Merge {{ $taxonomy->name }}
            </h2>
            <form method="GET" action="/admin/categories/merge">
                <label for="sort">Sort By:</label>
                <input type="hidden" name="taxonomy" value="{{ $taxonomy->id }}">
                <select class="custom-select" onchange="this.form.submit()" name="sort">
                    <option {{ (isset(request()->sort) && request()->sort == "name") ? 'selected' : '' }} value="name">Name</option>
                    <option {{ (isset(request()->sort) && request()->sort == "parent") ? 'selected' : '' }} value="parent">Grouping</option>
                    <option {{ (isset(request()->sort) && request()->sort == "id") ? 'selected' : '' }} value="id">Date of Creation</option>
                    <option {{ (isset(request()->sort) && request()->sort == "members") ? 'selected' : '' }} value="members">Members</option>
                </select>
            </form>
        </div>
    </div>
    <form method="post" action="/admin/categories/merge?taxonomy={{ $taxonomy->id }}">
        @csrf
        @method('put')
        <div class="col-md-8">
            <p class="text-muted">
                Check {{ Illuminate\Support\Str::lower($taxonomy->name) }} to merge together.
            </p>
            <table class="table mt-2">
                <thead>
                    <tr>
                        <th scope="col"><b>Name</b></th>
                        <th scope="col"><b>Grouping</b></th>
                        <th scope="col"><b>Members</b></th>
                        <th class="text-right">Merge</th>
                    </tr>
                </thead>
                @foreach($options as $option)
                <tr>
                    <td>{{ $option->name }}</td>
                    <td>{{ $option->parent }}</td>
                    <td class="text-right">{{ $option->users()->count() }}</td>
                    <td class="text-right">
                        <input type="checkbox" class="form-check-input" name="merge_from[]" value="{{ $option->id }}">
                    </td>
                </tr>
                @endforeach
            </table>
            <p class="text-muted">
                Pick a new name for this {{ Illuminate\Support\Str::lower(Illuminate\Support\Str::singular($taxonomy->name)) }}.
            </p>
            <label for="new_name">New Name: </label>
            <input type="text" class="form-control w-50 mb-2" name="new_name" required>
            <label for="parent">Grouping: </label>
            <div class="input-group mb-5 w-50">
                <select class="custom-select" name="parent">
                    @foreach($parents as $parent)
                        <option value="{{ $parent->parent }}">{{ $parent->parent }}</option>
                    @endforeach
                </select>
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">Merge</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@extends('admin.layout')

@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Reports' => '/admin/segments'
    ]])
    @endcomponent

    <div class="d-flex justify-content-between">
        <h5>Segments</h5>
        <div class="text-right">
            <a class="btn btn-primary btn-sm" href="/admin/segments/create">
              New segment
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
        @foreach($segments as $segment)
        <tr>
            <td>{{ $segment->name }}</td>
            <td class="text-right"><a href="/admin/segments/{{ $segment->id }}/demographics">View</a></td>
        </tr>
        @endforeach
    </table>
@endsection
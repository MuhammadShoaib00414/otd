@extends('admin.content.layout')

@section('inner-page-content')
    <div class="d-flex justify-content-between">
        <h5>Feeds</h5>
        <div class="text-right">
            <a class="btn btn-primary btn-sm" href="/admin/content/feeds/create">
              Add Feed
            </a>
        </div>
    </div>
    
    <table class="table mt-2">
        <thead>
            <tr>
                <th scope="col"><b>id</b></th>
                <th scope="col"><b>name</b></th>
                <th scope="col"><b>status</b></th>
                <th scope="col"><b>processed</b></th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($feeds as $feed)
            <tr>
                <td>{{ $feed->id }}</td>
                <td>{{ $feed->name }}</td>
                <td>{{ $feed->status }}</td>
                <td>{{ $feed->last_processed_at }}</td>
                <td class="text-right"><a href="/admin/content/feeds/{{ $feed->id }}/edit">Edit</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
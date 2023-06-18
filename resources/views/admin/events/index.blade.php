@extends('admin.events.layout')

@section('inner-page-content')    
    <div class="d-flex justify-content-end">
        <a class="btn btn-primary float-right mb-2" href="/admin/events/create">New event</a>
    </div>
    <table class="table mt-2">
        <thead>
            <tr>
                <th scope="col"><b>Name</b></th>
                <th scope="col"><b>Group</b></th>
                <th scope="col"><b>Date</b></th>
                <th colspan="2"></th>
            </tr>
        </thead>
        @foreach($events as $event)
        <tr>
            <td><a href="/admin/events/{{ $event->id }}">{{ $event->name }}</a></td>
            <td>
                @if($event->group)
                    <a href="/admin/groups/{{ $event->group->id }}">{{ $event->group->name }}</a>
                @endif
            </td>
            <td>{{ $event->date->tz(request()->user()->timezone)->format('m/d/y - g:i a') }}</td>
            <td>@if($event->trashed())<span class="badge badge-secondary">deleted</span>@endif</td>
            <td class="text-right"><a href="/admin/events/{{ $event->id }}/edit">Edit</a> - <a href="/admin/events/{{ $event->id }}">View</a></td>
        </tr>
        @endforeach
    </table>

    <div class="d-flex justify-content-center">
        {{ $events->links() }}
    </div>
@endsection
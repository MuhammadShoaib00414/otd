@extends('admin.layout')

@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Badges' => '',
    ]])
    @endcomponent

    <div class="d-flex justify-content-between">
        <h5>Badges</h5>
        <div class="text-right">
            <a class="btn btn-primary btn-sm" href="/admin/badges/create">
              Add Badge
            </a>
        </div>
    </div>

    <table class="table mt-2">
        <thead>
            <tr>
                <th scope="col" style="width: 25px;"></th>
                <th scope="col"><b>Name</b></th>
                <th scope="col"><b>Description</b></th>
                <th scope="col"><b>Status</b></th>
                <th scope="col"><b>Users</b></th>
                <th></th>
            </tr>
        </thead>
        @foreach($badges as $badge)
        <tr>
            <td style="width: 50px;">
                @if($badge->icon == null || $badge->icon == '/')
                  <div style="height: 3em;">@include('badges.default')</div>
                @else
                  <img src="{{ ltrim($badge->icon, '/') }}" style="height: 50px;">
                @endif
            </td>
            <td>{{ $badge->name }}<br></td>
            <td>{{ $badge->description }}<br></td>
            <td>
                @if($badge->is_enabled)
                    <span class="badge badge-primary">enabled</span>
                @else
                    <span class="badge badge-secondary">disabled</span>
                @endif
            </td>
            <td>{{ $badge->users()->count() }}</td>
            <td class="text-right"><a href="/admin/badges/{{ $badge->id }}/edit">Edit</a></td>
        </tr>
        @endforeach
    </table>
@endsection

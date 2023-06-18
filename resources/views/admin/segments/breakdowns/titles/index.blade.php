@extends('admin.segments.layout')

@section('inner-page-content')
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/admin/segments/{{ $segment->id }}">Segment: {{ $segment->name }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $title->name }} Breakdown</li>
      </ol>
    </nav>

    <div class="row">
        <div class="col-md-12">
            <h5 class="mb-3">Users who are a {{ $title->name }}</h5>
            <table class="table">
                <tr>
                    <td><b>user</b></td>
                    <td><b>users reporting</b></td>
                    <td></td>
                </tr>
                @foreach($users as $user)
                <tr>
                    <td>
                        <a href="/admin/segments/{{ $segment->id }}/demographics/titles/{{ $title->id }}/users/{{ $user->id }}">{{ $user->name }}</a>
                    </td>
                    <td>
                        {{ $user->count }}
                    </td>
                    <td class="text-right">
                        <a href="/admin/segments/{{ $segment->id }}/demographics/titles/{{ $title->id }}/users/{{ $user->id }}">View Reporting Users</a>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection
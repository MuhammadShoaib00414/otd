@extends('admin.segments.layout')

@section('inner-page-content')
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/admin/segments/{{ $segment->id }}">Segment: {{ $segment->name }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Groups Breakdown</li>
      </ol>
    </nav>

    <div class="row">
        <div class="col-md-12">
            <h5 class="mb-3">Breakdown of Groups in Segment <small>({{ $groups->count() }} total groups)</small></h5>
            <table class="table">
                <tr>
                    <td><b>group</b></td>
                    <td><b>total users</b></td>
                    <td></td>
                </tr>
                @foreach($groups as $group)
                <tr>
                    <td>
                        <a href="/admin/segments/{{ $segment->id }}/demographics/groups/{{ $group->id }}">{{ $group->name }}</a>
                    </td>
                    <td>
                        {{ $group->count }}
                    </td>
                    <td class="text-right">
                        <a href="/admin/segments/{{ $segment->id }}/demographics/groups/{{ $group->id }}">View users</a>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection
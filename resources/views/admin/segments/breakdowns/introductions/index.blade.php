@extends('admin.segments.layout')

@section('inner-page-content')
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/admin/segments/{{ $segment->id }}">Segment: {{ $segment->name }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Made an Introduction Breakdown</li>
      </ol>
    </nav>

    <div class="row">
        <div class="col-md-12">
            <h5 class="mb-3">Breakdown of Introduction Status in Segment</h5>
            <table class="table">
                <tr>
                    <td><b>status</b></td>
                    <td><b>total users</b></td>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        <a href="/admin/segments/{{ $segment->id }}/demographics/introductions/1">Has made an introduction</a>
                    </td>
                    <td>
                        {{ $introductions[1] }}
                    </td>
                    <td class="text-right">
                        <a href="/admin/segments/{{ $segment->id }}/demographics/introductions/1">View users</a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="/admin/segments/{{ $segment->id }}/demographics/introductions/0">Has not made an introduction</a>
                    </td>
                    <td>
                        {{ $introductions[0] }}
                    </td>
                    <td class="text-right">
                        <a href="/admin/segments/{{ $segment->id }}/demographics/introductions/0">View users</a>
                    </td>
                </tr>
            </table>
        </div>
    </div>
@endsection
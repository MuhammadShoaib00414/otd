@extends('admin.segments.layout')

@section('inner-page-content')
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/admin/segments/{{ $segment->id }}">Segment: {{ $segment->name }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $taxonomy->name }} Breakdown</li>
      </ol>
    </nav>

    <div class="row">
        <div class="col-md-12">
            <h5 class="mb-3">Breakdown of {{ $taxonomy->name }} in Segment <small>({{ $options->count() }} total {{ strtolower($taxonomy->name) }})</small></h5>
            <table class="table">
                <tr>
                    <td><b>option</b></td>
                    <td><b>total users</b></td>
                    <td></td>
                </tr>
                @foreach($options as $option)
                <tr>
                    <td>
                        <a href="/admin/segments/{{ $segment->id }}/demographics/taxonomies/{{ $taxonomy->id }}/options/{{ $option->id }}">{{ $option->name }}</a>
                    </td>
                    <td>
                        {{ $option->count }}
                    </td>
                    <td class="text-right">
                        <a href="/admin/segments/{{ $segment->id }}/demographics/taxonomies/{{ $taxonomy->id }}/options/{{ $option->id }}">View users</a>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection
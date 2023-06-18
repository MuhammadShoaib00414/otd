@extends('groups.layout')

@section('body-class', 'col-md-8')

@section('inner-content')
    <div class="row">
        <div class="col-md-12">
            <h5 class="mb-3">Breakdown of {{ $taxonomy->name }} <small>({{ $options->count() }} total {{ strtolower($taxonomy->name) }})</small></h5>
            <div class="card">
                <table class="table mb-0">
                <tr>
                    <td><b>option</b></td>
                    <td><b>total users</b></td>
                    @if($group->is_reporting_user_data_enabled)
                    <td></td>
                    @endif
                </tr>
                @foreach($options as $option)
                <tr>
                    <td>
                        @if($group->is_reporting_user_data_enabled)
                            <a href="/groups/{{ $group->slug }}/reports/demographics/taxonomies/{{ $taxonomy->id }}/options/{{ $option->id }}">{{ $option->name }}</a>
                        @else
                            {{ $option->name }}
                        @endif
                    </td>
                    <td>
                        {{ $option->count }}
                    </td>
                    @if($group->is_reporting_user_data_enabled)
                    <td class="text-right">
                        <a href="/groups/{{ $group->slug }}/reports/demographics/taxonomies/{{ $taxonomy->id }}/options/{{ $option->id }}">View users</a>
                    </td>
                    @endif
                </tr>
                @endforeach
            </table>
            </div>
        </div>
    </div>
@endsection
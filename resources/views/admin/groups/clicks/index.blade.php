@extends('admin.groups.layout')

@section('inner-page-content')
    <div class="col-8">
        <div class="card">
            <table class="table">
                <tr class="card-header">
                    <td><b>Action</b></td>
                    <td class="text-right"><b>Count</b></td>
                    <td></td>
                </tr>
                <tbody>
                    @foreach($clicks as $click)
                        <tr>
                            <td>{{ $click->action }}</td>
                            <td class="text-right">{{ $click->count }}</td>
                            <td class="text-right"><a href="/admin/groups/{{ $group->id }}/activity/{{ str_replace(' ', '-', $click->action) }}">view</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
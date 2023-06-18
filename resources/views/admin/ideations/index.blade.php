@extends('admin.ideations.layout')

@section('inner-page-content')

	<div class="d-flex justify-content-end">
        <a class="btn btn-primary float-right mb-2" href="/admin/ideations/create">New ideation</a>
    </div>
    <table class="table mt-2">
        <thead>
            <tr>
                <th scope="col"><b>Name</b></th>
                <th scope="col"><b>Owner</b></th>
                <th class="text-center" scope="col"><b>Approved</b></th>
                <th colspan="2"></th>
            </tr>
        </thead>
        @foreach($ideations as $ideation)
        <tr>
            <td><a href="/admin/ideations/{{ $ideation->id }}">{{ $ideation->name }}</a></td>
            <td><a href="/admin/users/{{ $ideation->owner()->pluck('id')->first() }}">{{ $ideation->owner()->pluck('name')->first() }}</a></td>
            <td class="text-center">
            	@if($ideation->is_approved)
            		<i class="fas fa-check"></i>
            	@endif
            </td>
            <td class="text-right"><a href="/admin/ideations/{{ $ideation->id }}/edit">Edit</a> - <a href="/admin/ideations/{{ $ideation->id }}">View</a></td>
        </tr>
        @endforeach
    </table>

@endsection
@extends('admin.layout')

@section('page-content')

    @component('admin.partials.breadcrumbs', ['links' => [
        'Registration Pages' => '/admin/registration',
    ]])
    @endcomponent

<div>
	<div class="d-flex justify-content-between mb-3 align-items-center">
		<h4 class="mb-0">Registration Pages</h4>
		<div>
			@include('admin.registration.partials.registration-image')
			<a href="/admin/registration/create" class="btn btn-primary btn-sm">Add Registration Page</a>
		</div>
	</div>
	<table class="table">
		<thead>
			<tr>
				<th scope="col">Name</th>
				<th scope="col">Description</th>
				<th class="text-center" scope="col">Accessible from home page</th>
				<th scope="col"></th>
			</tr>
		</thead>
		<tbody>
			@foreach($pages as $page)
			<tr>
				<td>{{ $page->name }}</td>
				<td>{{ str_limit($page->description, 75) }}</td>
				<td class="text-center">@if($page->is_welcome_page_accessible)<i class="fas fa-check"></i> @endif</td>
				<td class="text-center"><a href="/admin/registration/{{ $page->id }}">View</a></td>
			</tr>
			@endforeach
		</tbody>
	</table>



</div>
@endsection
@extends('admin.layout')

@section('page-content')
<div class="col-lg-8 col-md-10 col-sm-12">
	<h4>Tutorials</h4>
	<div class="card">
		<table class="table">
			<thead>
				<tr>
					<th>name</th>
					<th>url</th>
					<th class="text-right"></th>
				</tr>
			</thead>
			<tbody>
				@foreach($tutorials as $tutorial)
					<tr>
						<td>{{ $tutorial->name }}</td>
						<td><a href="{{ $tutorial->url }}">{{ $tutorial->url }}</a></td>
						<td class="text-right"><a href="/admin/tutorials/{{ $tutorial->id }}/edit">Edit</a></td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@endsection
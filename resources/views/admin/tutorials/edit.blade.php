@extends('admin.layout')

@section('page-content')
<div class="col-lg-8 col-md-10 col-sm-12">
	<h4>Edit {{ $tutorial->name }} Tutorial</h4>
	<form action="/admin/tutorials/{{ $tutorial->id }}" method="post">
		@csrf
		@method('put')
		<div class="form-group">
			<label for="url">Url</label>
			<input type="text" class="form-control" name="url" id="url" value="{{ $tutorial->url }}">
		</div>
		<button type="submit" class="btn btn-primary">@lang('general.save')</button>
	</form>
</div>
@endsection
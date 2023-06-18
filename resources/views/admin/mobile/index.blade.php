@extends('admin.layout')

@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Mobile App' => '',
    ]])
    @endcomponent

<div class="container w-100 mb-3">
	<h2>Mobile App</h2>
	<div class="card card-body">
		<h4>Demo:</h4>
		@include('components.mobilenav', ['links' => $links])
		<hr>

		<form action="/admin/mobile/edit" method="post" enctype="multipart/form-data">
			@csrf
			@method('put')
			@foreach($links as $link)
				<div class="form-group w-50">
					<label>Url</label>
					<span class="d-none not-valid-text row ml-2" style="color: red">URL must include {{ config('app.url') }}</span>
					<input {{ $link->is_editable ? '' : 'disabled' }} type="text" class="form-control urlInput" value="{{ config('app.url') }}{{ $link->url }}" name="links[{{ $link->id }}][url]" required> 
				</div>
				<div class="form-group">
					<label class="d-flex mb-2">Icon</label>
					<image class="d-flex mb-2" style="max-width: 50px;" src="{{ $link->icon_url }}">
					<input class="d-flex mb-2" type="file" accept="image/png, image/jpg, image/jpeg" name="links[{{ $link->id }}][icon]">
				</div>
				<div class="form-check">
					<input id="links[{{ $link->id }}][revert]" type="checkbox" class="form-check-input" name="links[{{ $link->id }}][revert]">
					<label for="links[{{ $link->id }}][revert]">Revert to defaults</label>
				</div>
				<hr>
			@endforeach
			<button type="submit" class="btn btn-primary" id="saveButton">Save</button>
		</form>
	</div>
</div>
@endsection

@section('scripts')
<script>
	$('.urlInput').keyup(function() {
		if(!$(this).val().includes("{{ config('app.url') }}"))
		{
			$(this).parent().find('.not-valid-text').removeClass('d-none');
			$('#saveButton').prop('disabled', true);
		}
		else
		{
			$('#saveButton').prop('disabled', false);
			$(this).parent().find('.not-valid-text').addClass('d-none');
		}
	});
</script>
@endsection
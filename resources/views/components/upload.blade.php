<div id="{{ $name }}_container">
	<div class="d-flex {{ $value ? 'my-3' : 'd-none' }}">
		 <img id="image_{{ $name }}" style="max-height: 86px;" src="{{ $value }}">
		 <small id="new_file_{{ $name }}" class="text-muted d-none"></small>
	</div>
	<div class="d-flex align-items-center mb-2">
		<input class="d-none" name="{{ $name }}" type="file" id="{{ $name }}" {{ isset($accept) ? 'accept='.$accept : '' }}/>
		<label id="label_{{ $name }}" class="mb-0 mr-2" for="{{ $name }}"><span id="button_{{ $name }}" class="btn btn-sm btn-outline-primary">@if($value) @lang('general.change') @else @lang('general.choose-file') @endif</span></label>
	</div>
</div>
@if(isset($noRemove) && !$noRemove)
<div class="row form-check mb-1 pl-3">
	<input type="checkbox" name="{{ $name }}_remove" id="{{ $name }}_remove">
	<label class="form-check-label ml-2" for="{{ $name }}_remove"> @lang('general.remove-image')</label>
</div>
@endif

@push('scriptstack')
<script>
	$('#{{ $name }}').change( function (e) {
		$('#image_{{ $name }}').addClass('d-none');
		console.log($(this).val().substring($(this).val().lastIndexOf('\\') + 1));
		$('#new_file_{{ $name }}').html($(this).val().substring($(this).val().lastIndexOf('\\') + 1));
		$('#new_file_{{ $name }}').removeClass('d-none');
		$('#button_{{ $name }}').html(__('general.change'));
	});
	$('#{{ $name }}_remove').change(function (e) {
		if($(this).is(':checked'))
			$('#{{ $name }}_container').addClass('d-none');
		else
			$('#{{ $name }}_container').removeClass('d-none');
	});
</script>
@endpush
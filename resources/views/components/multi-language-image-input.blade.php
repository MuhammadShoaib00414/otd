<div class="w-100 d-flex justify-content-around">
	<div>
		@if(getsetting('is_localization_enabled'))
			<p>@lang('general.english-version')</p>
		@endif
		<div id="{{ $name }}_container">
			<div class="d-flex">
				 @if(isset($value) && $value)
				 	<img id="image_{{ $name }}" style="max-width: {{ isset($maxWidth) ? $maxWidth : '80px' }};" src="{{ $value }}">
				 @endif
				 <small id="new_file_{{ $name }}" class="text-muted d-none"></small>
			</div>
			<div class="d-flex align-items-center mb-2">
				<input class="d-none" name="{{ $name }}" type="file" id="{{ $name }}"/>
				<label id="label_{{ $name }}" class="mb-0 mr-2" for="{{ $name }}"><span id="button_{{ $name }}" class="btn btn-sm btn-outline-primary">@if(isset($value) && $value) @lang('general.change') @else @lang('general.choose-file') @endif</span></label>
			</div>
		</div>
		@if(isset($noRemove) && !$noRemove)
		<div class="row form-check mb-1 pl-3">
			<input type="checkbox" name="{{ $name }}_remove" id="{{ $name }}_remove">
			<label class="form-check-label ml-2" for="{{ $name }}_remove"> @lang('general.remove-image')</label>
		</div>
		@endif
		@if(isset($revert) && $revert)
			<div class="row form-check mb-1 pl-2 mt-3">
				<input class="image_revert" type="checkbox" name="{{ $name }}_revert" id="{{ $name }}_revert">
				<label class="form-check-label ml-2" for="{{ $name }}_revert"> Revert to original</label>
			</div>
		@endif
	</div>
	<div>
		@if(getsetting('is_localization_enabled'))
			<p>@lang('general.spanish-version')</p>
			<?php $isset = isset($localization) && isset($localization['es']) && isset($localization['es'][$name]) ?>
			<div id="{{ $name }}_container_es">
				<div class="d-flex {{ $isset ? 'my-3' : 'd-none' }}">
					 <img id="image_{{ $name }}_es" style="max-height: 86px;" src="{{ $isset ? getS3Url($localization['es'][$name]) : '' }}">
					 <small id="new_file_{{ $name }}_es" class="text-muted d-none"></small>
				</div>
				<div class="d-flex align-items-center mb-2">
					<input class="d-none" name="{{ $name }}_localization[es][{{ $name }}]" type="file" id="localized_{{ $name }}"/>
					<label id="label_{{ $name }}_es" class="mb-0 mr-2" for="localized_{{ $name }}"><span id="button_{{ $name }}_es" class="btn btn-sm btn-outline-primary">@if($isset) @lang('general.change') @else @lang('general.choose-file') @endif</span></label>
				</div>
			</div>
		@endif
		@if(isset($noRemove) && !$noRemove)
		<div class="row form-check mb-1 pl-3">
			<input type="checkbox" name="{{ $name }}_remove_es" id="{{ $name }}_remove_es">
			<label class="form-check-label ml-2" for="{{ $name }}_remove_es"> @lang('general.remove-image')</label>
		</div>
		@endif
		@if(isset($revert) && $revert)
			<div class="row form-check mb-1 pl-2 mt-3">
				<input class="image_revert" type="checkbox" name="{{ $name }}_revert_es" id="{{ $name }}_revert_es">
				<label class="form-check-label ml-2" for="{{ $name }}_revert_es"> Revert to original</label>
			</div>
		@endif
	</div>
</div>

@push('scriptstack')
<script>
	$('#{{ $name }}').change( function (e) {
		console.log('lol');
		$('#image_{{ $name }}').addClass('d-none');
		$('#new_file_{{ $name }}').html($(this).val().substring($(this).val().lastIndexOf('\\') + 1));
		$('#new_file_{{ $name }}').removeClass('d-none');
		$('#button_{{ $name }}').html("@lang('general.change')");
	});

	$('#localized_{{ $name }}').change( function (e) {
		console.log('hah');
		$('#image_{{ $name }}_es').addClass('d-none');
		$('#new_file_{{ $name }}_es').html($(this).val().substring($(this).val().lastIndexOf('\\') + 1));
		$('#new_file_{{ $name }}_es').removeClass('d-none');
		$('#button_{{ $name }}_es').html("@lang('general.change')");
	});

	$('#{{ $name }}_remove').change(function (e) {
		if($(this).is(':checked'))
			$('#{{ $name }}_container').addClass('d-none');
		else
			$('#{{ $name }}_container').removeClass('d-none');
	});

	$('#{{ $name }}_remove_es').change(function (e) {
		if($(this).is(':checked'))
			$('#{{ $name }}_container_es').addClass('d-none');
		else
			$('#{{ $name }}_container_es').removeClass('d-none');
	});

	$('.image_revert').change(function () {
	    if($(this).prop('checked')) {
	      $('label[for="'+ $(this).prop('id') +'"]').css('color', '#c71c1c');
	    } else {
	      $('label[for="'+ $(this).prop('id') +'"]').css('color', '#363a3d');
	    }
	  });
</script>
@endpush
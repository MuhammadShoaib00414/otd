@extends('admin.layout')

@section('page-content')
<div class="p-3 mb-5">
	<div class="d-flex flex-row align-items-center justify-content-between">
		<h4>Onboarding Settings</h4>
		<a href="/onboarding" target="_blank" class="btn btn-sm btn-outline-primary">Go to onboarding</a>
	</div>
	<form action="/admin/onboarding" method="POST" class="w-50">
		@method('post')
		@csrf

		<hr>

		<h5 class="mt-4">Intro (step 0)</h5>
		<div>
			<div class="form-group my-3">
				<label for="settings[intro][title]">Title</label>
				<input type="text" class="form-control" name="settings[intro][title]" id="settings[intro][title]" value="{{ $settings['intro']['title'] }}" required>
			</div>

			<div class="form-group my-3">
				<label for="settings[intro][prompt]">Prompt</label>
				<input type="text" class="form-control" name="settings[intro][prompt]" id="settings[intro][prompt]" value="{{ $settings['intro']['prompt'] }}" required>
			</div>

			<div class="form-group my-3">
				<label for="settings[intro][description]">Description</label>
				<textarea type="text" class="form-control" name="settings[intro][description]" id="settings[intro][description]" required>{{ $settings['intro']['description'] }}</textarea>
			</div>
		</div>

		<hr>

		<h5 class="mt-4">Embed an Introduction Video</h5>
		<div>
			<div class="form-group my-3">
				<label for="settings[embed_video]">Embedded video</label>
				<br>
				<small class="text-muted">If this is set, the video will display between steps 0 and 1.</small>
				<textarea type="text" class="form-control" name="settings[embed_video]" id="settings[embed_video]">{{ $settings['embed_video'] }}</textarea>
			</div>
		</div>

		<hr>

		<h5 class="mt-4">Basic (step 1)</h5>
		<div>
			<div class="form-group my-3">
				<label for="settings[basic][title]">Title</label>
				<input type="text" class="form-control" name="settings[basic][title]" id="settings[basic][title]" value="{{ $settings['basic']['title'] }}" required>
			</div>

			<div class="form-group my-3">
				<label for="settings[basic][prompt]">Prompt</label>
				<input type="text" class="form-control" name="settings[basic][prompt]" id="settings[basic][prompt]" value="{{ $settings['basic']['prompt'] }}" required>
			</div>
		</div>

		<hr>

		<h5 class="mt-4">Profile Picture / Bio (step 2)</h5>
		<div>
			<div class="form-group my-3">
				<label for="settings[imagebio][title]">Title</label>
				<input type="text" class="form-control" name="settings[imagebio][title]" id="settings[imagebio][title]" value="{{ $settings['imagebio']['title'] }}" required>
			</div>

			<div class="form-group my-3">
				<label for="settings[imagebio][prompt]">Prompt</label>
				<input type="text" class="form-control" name="settings[imagebio][prompt]" id="settings[imagebio][prompt]" value="{{ $settings['imagebio']['prompt'] }}" required>
			</div>
		</div>

		<hr>

		<h5 class="mt-4">About (step 3)</h5>
		<div>
			<div class="form-group my-3">
				<label for="settings[about][title]">Title</label>
				<input type="text" class="form-control" name="settings[about][title]" id="settings[about][title]" value="{{ $settings['about']['title'] }}" required>
			</div>

			<div class="form-group my-3">
				<label for="settings[about][prompt]">Prompt</label>
				<input type="text" class="form-control" name="settings[about][prompt]" id="settings[about][prompt]" value="{{ $settings['about']['prompt'] }}" required>
			</div>
		</div>

		<hr>

		<h5 class="mt-4">Questions (step 4)</h5>
		<div>
			<div class="form-group my-3">
				<label for="settings[questions][prompt]">Title</label>
				<input type="text" class="form-control" name="settings[questions][prompt]" id="settings[questions][prompt]" value="{{ $settings['questions']['prompt'] }}" required>
			</div>

			<div class="form-group my-3">
				<label for="settings[questions][description]">Description</label>
				<textarea type="text" class="form-control" name="settings[questions][description]" id="settings[questions][description]">{{ $settings['questions']['description'] }}</textarea>
			</div>
		</div>

		<hr>

		@foreach($taxonomies as $taxonomy)
			<div>
				<h5> Category: {{ $taxonomy->name }}</h5>
				<div class="form-group">
					<label for="settings[taxonomies][{{ $taxonomy->id }}][description]">Description</label>
					<textarea class="form-control" name="settings[taxonomies][{{ $taxonomy->id }}][description]" id="settings[taxonomies][{{ $taxonomy->id }}][description]">@if(array_key_exists('taxonomies', $settings) && array_key_exists($taxonomy->id, $settings['taxonomies']) && array_key_exists('description', $settings['taxonomies'][$taxonomy->id])){!! $settings['taxonomies'][$taxonomy->id]['description'] !!}@endif</textarea>
				</div>
			</div>

			<hr>
		@endforeach


		<button class="btn btn-primary" type="submit">Save Changes</button>
	</form>
</div>
@endsection
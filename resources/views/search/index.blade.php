@extends('layouts.app')

@section('content')
<div class="col-12 col-md-10 col-lg-8 mx-md-auto pt-2">
	<a href="/home" style="font-size: 0.9em;"> < @lang('messages.my-dashboard') </a>
	<div class="card card-body mt-2 px-4">
		<form action="/search" method="GET" class="mb-3">
			<div class="input-group input-group-lg mx-auto" style="max-width: 600px;">
				<input required type="text" name="q" id="q" class="form-control" value="{{ request()->has('q') ? request()->q : '' }}">
				<div class="input-group-append">
					<button type="submit" class="btn btn-secondary pl-3"><i class="icon icon-magnifying-glass mx-2"></i></button>
				</div>
			</div>
			@if(request()->has('q') && $results->count())
			<div class="text-center mt-1">
				<p style="font-size: 0.75em;">{{ $results->count() }} results for <b>{{ '"' . request()->q . '"' }}</b></p>
			</div>
			@endif
		</form>
		<table class="table" style="table-layout: fixed">
			<tbody>
				@if($results->count())
					@each('search.result', $results, 'result')
				@elseif(request()->q == '' || !request()->has('q'))
					<p class="mx-auto mt-2">@lang('messages.enter-search-term')</p>
				@else
					<p class="mx-auto mt-2">@lang('messages.your-search-for') "<b>{{ request()->q }}</b>" @lang('messages.had-no-results').</p>
				@endif
			</tbody>
		</table>
	</div>
</div>
@endsection
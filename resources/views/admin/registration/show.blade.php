@extends('admin.registration.layout')

@section('inner-page-content')

<div class="py-2 px-4 col-8">
	<p class="mr-2" style="word-wrap: break-word;">{{ $page->description }}</p>
	<div class="card card-body">
		<span class="mb-2"><b>Prompt:</b> {{ $page->prompt }}</span>
		@if($page->event_name && $page->event_date && $page->event_end_date)
			<span class="mb-2"><b>Event:</b> {{ $page->name }} on {{ $page->event_date->format('jS \\of F') }} @ {{ $page->event_date->format('h:i A') }} - {{ $page->event_date->day == $page->event_end_date->day ? $page->event_end_date->format('h:i A') : $page->event_end_date->format('jS \\of F') . ' @ ' . $page->event_end_date->format('h:i A') }}</span>
		@endif
		@if($page->is_welcome_page_accessible)
			<span><i class="fas fa-check"></i> Accessible from Welcome Page</span>
		@else
			<span class="text-muted"><i class="fas fa-times"></i> Not accessible from Welcome Page</span>
		@endif
		@if($page->is_event_only)
			<span><i class="fas fa-check"></i> Limited access</span>
		@else
			<span><i class="fas fa-check"></i> Standard access</span>
		@endif
		@if($page->assign_to_groups)
			@if($page->is_event_only)
				<p class="mt-2 mb-0">Limit access to groups:</p>
			@else
				<p class="mt-2 mb-0">Assign to groups:</p>
			@endif
			<ul>
				@foreach($page->groups as $group)
					<li>{{ $group->name }}</li>
				@endforeach
			</ul>
		@endif
	</div>
	</div>
</div>
@endsection
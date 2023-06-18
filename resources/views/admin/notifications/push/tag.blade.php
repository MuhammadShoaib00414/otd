<div class="d-flex">
	<div class="col px-0">
		<div class="row ml-0"><b>{{ $index }}</b></div>
		<div class="row ml-0">{{ $description }}</div>
		@if(!$isLast)
		<hr>
		@endif
	</div>
</div>
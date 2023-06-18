<div class="w-100 d-flex justify-content-between">
	@foreach($links as $link)
		<a href="{{ $link->url }}" class="col text-center">
			<img class="mx-auto" src="{{ $link->icon_url }}" style="max-width: 30px;">
		</a>
	@endforeach
</div>
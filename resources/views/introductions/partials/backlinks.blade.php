<div>
	@if(Request::is('/introductions/s*') || Request::is('/introductions/r*'))
		<a href="/home" class="d-inline-block" style="font-size: 14px;"><i class="icon-chevron-small-left"></i> @lang('messages.my-dashboard')</a>
	@else
	<div class="d-flex flex-nowrap">
		<a href="/home" class="d-inline-block mr-1" style="font-size: 14px;"> @lang('messages.my-dashboard')</a>
		<i class="icon-chevron-small-right mr-1"></i>
		<a href="/introductions/received" class="d-inline-block" style="font-size: 14px;">@lang('introductions.introductions')</a>

	</div>
	@endif
</div>
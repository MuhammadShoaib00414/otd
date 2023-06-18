<div class="pt-1 d-flex flex-nowrap" style="position: absolute;{{ $group->has_home_image ? 'transform: translateY(-50%);' : 'color:#fff;transform: translateY(-50%);'}}">
  @if(!request()->user()->is_event_only)
  <a href="{{ route('spa') }}" class="mb-2" style="white-space: nowrap;font-size: 14px; top: 2.5em;{{ $group->has_home_image || request()->is('*lounge*') ? '' : 'color:#fff;transform: translateY(20%);'}}">
  	@if(!$group->parent_group_id && !Request::is('*groups/*/*'))
  		<i class="icon-chevron-small-left"></i>
  	@endif
  	 @lang('messages.my-dashboard')</a>
  @endif
  @if($group->parent && $group->parent->parent)
    <a href="/groups/{{ $group->ancestor->slug }}" class="mb-2" style="white-space: nowrap;font-size: 14px; top: 2.5em;{{ $group->has_home_image || request()->is('*lounge*') ? '' : 'color:#fff;transform: translateY(20%);'}}">
      @if(!request()->user()->is_event_only)<i class="icon-chevron-small-right"></i>@endif {{ $group->ancestor->name }}</a>
    <a href="/groups/{{ $group->parent->slug }}" class="mb-2" style="white-space: nowrap;font-size: 14px; top: 2.5em;{{ $group->has_home_image || request()->is('*lounge*') ? '' : 'color:#fff;transform: translateY(20%);'}}"><i class="icon-chevron-small-right"></i> {{ $group->parent->name }}</a>
    <a href="/groups/{{ $group->parent->slug }}/subgroups" class="d-inline-block mb-2" style="font-size: 14px; top: 2.5em; {{ $group->has_home_image || request()->is('*lounge*') ? '' : 'color:#fff;transform: translateY(20%);'}} white-space: nowrap;"><i class="icon-chevron-small-right"></i> {{ $group->parent->subgroups_page_name }}</a>
  @elseif($group->parent)
  	<a href="/groups/{{ $group->parent->slug }}" class="mb-2" style="white-space: nowrap;font-size: 14px; top: 2.5em;{{ $group->has_home_image || request()->is('*lounge*') ? '' : 'color:#fff;transform: translateY(20%);'}}">@if(!request()->user()->is_event_only)<i class="icon-chevron-small-right"></i>@endif {{ $group->parent->name }}</a>
  	<a href="/groups/{{ $group->parent->slug }}/subgroups" class="d-inline-block mb-2" style="font-size: 14px; top: 2.5em; {{ $group->has_home_image || request()->is('*lounge*') ? '' : 'color:#fff;transform: translateY(20%);'}} white-space: nowrap;"><i class="icon-chevron-small-right"></i> {{ $group->parent->subgroups_page_name }}</a>
  @endif
  @if(Request::is('*groups/*/*') || Request::is('*lounge*'))
  	<a href="/groups/{{ $group->slug }}" class="mb-2" style="white-space: nowrap;font-size: 14px; top: 2.5em; {{ $group->has_home_image || request()->is('*lounge') ? '' : 'color:#fff;transform: translateY(20%);'}}">@if(!request()->user()->is_event_only || $group->parent)<i class="icon-chevron-small-right"></i>@else <i class="icon-chevron-small-left"></i> @endif {{ $group->name }}</a>
  @endif
</div>
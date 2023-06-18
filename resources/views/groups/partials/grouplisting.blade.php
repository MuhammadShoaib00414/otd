<div class="nav flex-column">
	
  <a class="nav-link" href="/spa#/groups/{{ $group->slug }}">{{ $group->name }}</a>
</div>
@if($group->hasAccessableSubgroups(request()->user()->id))
<div class="ml-2">
	@foreach($group->subgroupsUserIsMemberOf(request()->user())->where('should_display_dashboard', 1)->get() as $subgroup)
		@include('groups.partials.grouplisting', ['group' => $subgroup])
	@endforeach
</div>
@endif
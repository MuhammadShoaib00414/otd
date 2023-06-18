@foreach($group->subgroups()->orderBy('order_key', 'asc')->get() as $subgroup)
	@if($subgroup->should_display_dashboard)
		<a href="/admin/groups/{{ $subgroup->id }}/subgroups" target="_blank">{{ str_repeat('-', $count) }} {{ $subgroup->name }}</a>
	@endif
	@include('admin.groups.partials.indexRecursiveSort', ['group' => $subgroup, 'count' => $count + 1])
@endforeach
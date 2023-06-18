@foreach($group->subgroups->sortBy('order_key') as $subgroup)
	@if($subgroup->should_display_dashboard && $subgroup->users()->where('id', request()->user()->id)->count())
		<div style="margin-left: {{ 7 * $count }}px;" class="nav flex-column">
            <a class="nav-link" href="/groups/{{ $subgroup->slug }}">{{ $subgroup->name }}</a>
        </div>
	@endif
	@include('partials.dashboardSubgroups', ['group' => $subgroup, 'count' => $count + 1])
@endforeach
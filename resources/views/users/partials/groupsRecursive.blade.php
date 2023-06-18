@foreach($group->subgroups as $subgroup)
	@if($subgroup->users()->where('id', request()->user()->id)->count())
		<li style="margin-left: {{ 15 * $count }}px;" class="mt-2"><a href="/groups/{{ $subgroup->slug }}"> {{ $subgroup->name }}</a></li>
		@include('users.partials.groupsRecursive', ['group' => $subgroup, 'count' => $count + 1])
	@endif
@endforeach
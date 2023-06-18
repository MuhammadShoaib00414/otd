@extends('admin.layout')

@push('stylestack')
<style>
td {
	 text-align: center; 
	 vertical-align: middle;
}
.th-cell {
	font-size: 0.8em;
}
input {
    display: inline;
}
.pageName {
	min-width: 200px;
}
</style>
@endpush

@section('page-content')

	@component('admin.partials.breadcrumbs', ['links' => [
        'Groups' => '/admin/groups',
        'Bulk Edit Settings' => '/admin/groups/bulk-settings',
    ]])
    @endcomponent

@if(session()->has('success'))
    <div class="text-center mb-4 alert-success py-3">
        {{ session('success') }}
    </div>
@endif
<form action="/admin/groups/bulk-settings" method="post" style="overflow-x: scroll; display: block">
	@csrf
	@method('put')
	<table class="table table-bordered table-sm">
		<thead>
			<tr>
				<th colspan="7"></th>
				<th colspan="7" class="text-center">Allow group admins to...</th>
				<th colspan="5" class="text-center">Allow users to...</th>
				<th colspan="9"></th>
			</tr>
			<tr>
				<th></th>
				<th class="text-center th-cell">Private group</th>
				<th class="text-center th-cell">Publish posts to parent feed</th>
				<th class="text-center th-cell">Interactive header image</th>
				<th class="text-center th-cell">Networking lounge</th>
				<th class="text-center th-cell">Video conferencing in networking lounge</th>
				<th class="text-center th-cell">Live chat</th>

				<th class="text-center th-cell">Send email campaigns</th>
				<th class="text-center th-cell">Toggle content types within group</th>
				<th class="text-center th-cell">Access reporting</th>
				<th class="text-center th-cell">Access user breakdown in reports</th>
				<th class="text-center th-cell">Toggle & manage live chat</th>
				<th class="text-center th-cell">Toggle & manage interactive header image</th>
				<th class="text-center th-cell">Invite other groups to events</th>

				<th class="text-center th-cell">Post Events</th>
				<th class="text-center th-cell">Post Shoutouts</th>
				<th class="text-center th-cell">Post Content</th>
				<th class="text-center th-cell">Post Text</th>
				<th class="text-center th-cell">Upload Files</th>

				<th class="text-center th-cell">Dashboard Header</th>

				<th class="text-cetner th-cell">Group home page name</th>
				<th class="text-cetner th-cell">Posts page name</th>
				<th class="text-cetner th-cell">Content page name</th>
				<th class="text-cetner th-cell">Calendar page name</th>
				<th class="text-cetner th-cell">Shoutouts page name</th>
				<th class="text-cetner th-cell">Discussions page name</th>
				<th class="text-cetner th-cell">Subgroups page name</th>
				<th class="text-cetner th-cell">Members page name</th>
				<th class="text-cetner th-cell">Files page name</th>
			</tr>
		</thead>
		<tbody>
			@foreach($groups as $group)
				@include('admin.groups.partials.bulkSettings.subgroupsRecursive', ['group' => $group])
			@endforeach
		</tbody>
	</table>
	<button type="submit" class="btn btn-primary mb-4">@lang('general.save') changes</button>
</form>
@endsection

@section('scripts')
<script>
$('input[type=checkbox]').change(function (e) {
	if($(this).is(':checked'))
		disableSibling(this);
	else
		enableSibling(this);
});

function enableSibling(el)
{
	$(el).next().attr('disabled', false);
}

function disableSibling(el)
{
	$(el).next().attr('disabled', true);
}
</script>
@endsection
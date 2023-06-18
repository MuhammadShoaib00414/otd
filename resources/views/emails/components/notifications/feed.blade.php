
<table style="max-width: 200px; margin-left:auto; margin-right:auto;">
	@foreach($notifications->groupBy('action') as $notification)
	<tr>
		<td>{{ $notification->count() }} {{ str_plural($notification->first()->action, $notification->count()) }}</td>
	</tr>
	@endforeach
</table>
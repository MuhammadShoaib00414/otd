@extends('admin.emails.layout')

@section('inner-page-content')
<table class="table">
    <tr>
        <td><b>notification</b></td>
        <td><b>title</b></td>
        <td><b>body</b></td>
        <td><b>enabled</b></td>
        <td></td>
    </tr>
    @foreach($notifications as $notification)
        <tr>
            <td style="vertical-align: middle;">{{ $notification->name }}</td>
            <td style="vertical-align: middle;">{{ $notification->title }}</td>
            <td style="vertical-align: middle;">{{ $notification->body }}</td>
            <td class="text-center" style="vertical-align: middle;">@if($notification->is_enabled) <i class="fas fa-check"></i> @endif</td>
            <td style="vertical-align: middle;">
                <a href="/admin/notifications/push/{{ $notification->id }}/edit">Edit</a>
            </td>
        </tr>
    @endforeach
</table>
@endsection
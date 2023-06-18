@extends('admin.emails.layout')

@section('inner-page-content')
    <table class="table">
        <tr>
            <td><b>name</b></td>
            <td><b>description</b></td>
            <td class="text-center"><b>enabled</b></td>
            <td></td>
        </tr>
        @foreach($emails as $email)
            <tr>
                <td style="vertical-align: middle;">{{ $email->name }}</td>
                <td style="vertical-align: middle;">{{ $email->description }}</td>
                <td class="text-center" style="vertical-align: middle;">@if($email->is_enabled) <i class="fas fa-check"></i> @endif</td>
                <td style="vertical-align: middle;">
                    <a href="/admin/emails/notifications/{{ $email->id }}" class="mr-2">View</a>
                    <a href="/admin/emails/notifications/{{ $email->id }}/edit">Edit</a>
                </td>
            </tr>
        @endforeach
    </table>
@endsection
@extends('admin.layout')

@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Onboarding Emails' => '/admin/emails/welcome',
    ]])
    @endcomponent

<div>
    <div class="d-flex justify-content-between mb-3">
        <h5>Onboarding Emails</h5>
        <a href="/admin/emails/welcome/create" class="btn btn-sm btn-primary"><i class="fa fa-plus mr-2"></i>New</a>
    </div>
    <table class="table">
        <tr>
            <td><b>days</b></td>
            <td><b>email subject</b></td>
            <td><b>total sent</b></td>
            <td><b>status</b></td>
            <td></td>
        </tr>
        @foreach ($emails as $email)
        <tr>
            <td>{{ $email->send_after_days }}</td>
            <td>{{ $email->email_subject }}</td>
            <td>{{ $email->total_sent }}</td>
            <td>
                @if($email->enabled)
                    <span class="badge badge-primary">active</span>
                @else
                    <span class="badge badge-secondary">not active</span>
                @endif
            </td>
            <td style="text-align: right;"><a href="/admin/emails/welcome/{{ $email->id }}/edit">Edit</a> | <a href="/admin/emails/welcome/{{ $email->id }}">View</a></td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
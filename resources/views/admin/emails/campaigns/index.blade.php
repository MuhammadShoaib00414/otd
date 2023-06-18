@extends('admin.layout')

@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Email Campaigns' => '/admin/emails/campaigns',
    ]])
    @endcomponent

<div>
    <div class="d-flex justify-content-between mb-3">
        <h5>Campaigns</h5>
        <a href="/admin/emails/campaigns/create" class="btn btn-sm btn-primary"><i class="fa fa-plus mr-2"></i>New</a>
    </div>
    <table class="table">
        <tr>
            <td><b>id</b></td>
            <td><b>Email Subject</b></td>
            <td><b>Created At</b></td>
            <td><b>Status</b></td>
            <td class="text-right"><b>Total Sent</b></td>
            <td></td>
        </tr>
        @foreach($campaigns as $campaign)
        <tr>
            <td style="vertical-align: middle;">{{ $campaign->id }}</td>
            <td>
                {{ $campaign->email_subject }}
                @if($campaign->group)
                <p class="mb-0 text-small text-muted"><a href="/admin/groups/{{ $campaign->group->id }}" class="text-small text-muted">{{ $campaign->group->name }}</a></p>
                @endif
            </td>
            <td style="vertical-align: middle;">{{ $campaign->created_at->tz($authUser->timezone)->format('F j, Y - g:ia') }}</td>
            <td style="vertical-align: middle;">
                @if($campaign->status == 'sent')
                    <span class="badge badge-primary">sent</span>
                @else
                    <span class="badge badge-secondary">{{ $campaign->status }}</span>
                @endif
            </td>
            <td class="text-right" style="vertical-align: middle;">{{ $campaign->total_sent }}</td>
            <td class="text-right" style="vertical-align: middle;">
                <a href="/admin/emails/campaigns/{{ $campaign->id }}">
                    @if($campaign->status == 'sent')
                        View
                    @else
                        Edit/Send
                    @endif
                </a>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
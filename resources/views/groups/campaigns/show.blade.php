@extends('groups.layout')

@section('stylesheets')
@parent
<style>
.hover-hand:hover { cursor: pointer; }
</style>
@endsection

@section('body-class', 'col-md-9')

@section('inner-content')
    <div class="d-flex mb-3 justify-content-between align-items-center mt-3">
        <div>
            <h4 class="mb-0">{{ $campaign->email_subject }}</h4>
            @if($campaign->status == 'sent')
                <span class="badge badge-primary">@lang('campaigns.sent')</span>
                <span>- {{ $campaign->total_sent }} @lang('campaigns.emails sent at') {{ $campaign->sent_at->format('F j, Y - g:ia') }}</span>
            @elseif($campaign->status == 'scheduled')
                <span class="badge badge-secondary">@lang('campaigns.scheduled')</span>
                <span>- @lang('campaigns.scheduled to send at') {{ $campaign->send_at->tz(request()->user()->timezone)->format('m/d/y g:i a') }}</span>
            @else
                <span class="badge badge-secondary">{{ $campaign->status }}</span>
            @endif
        </div>
        <div class="d-flex justify-content-end">
            @if($campaign->status == 'not sent')
            <a href="/groups/{{ $group->slug }}/email-campaigns/{{ $campaign->id }}/edit" class="btn btn-light mr-2">@lang('general.edit')</a>
            <div class="dropdown">
              <a href="/admin/emails/campaigns/{{ $campaign->id }}/send" class="btn btn-sm btn-primary dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                @lang('campaigns.Send')
              </a>
              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="/groups/{{ $group->slug }}/email-campaigns/{{ $campaign->id }}/select-recipients">@lang('campaigns.Send now')</a>
                <a class="dropdown-item" href="/groups/{{ $group->slug }}/email-campaigns/{{ $campaign->id }}/schedule">@lang('campaigns.Schedule')</a>
              </div>
            </div>
            @endif
        </div>
    </div>
    <div class="card">
        <iframe src="/groups/{{ $group->slug }}/email-campaigns/{{ $campaign->id }}/html" style="width: 100%; height: 500px;"></iframe>
    </div>
@endsection
@extends('groups.layout')

@section('stylesheets')
@parent
<style>
.hover-hand:hover { cursor: pointer; }
</style>
@endsection

@section('body-class', 'col-md-9')

@section('inner-content')

    @if(session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    <div class="d-flex mb-3 justify-content-between align-items-center">
        <div class="d-flex">
            <h4 class="mb-0 mr-2">@lang('campaigns.Email Campaigns')</h4>
            <div style="padding-top: 0.2em;">
                @include('partials.tutorial', ['tutorial' => \App\Tutorial::named('Email Campaigns')])
            </div>
        </div>
        <a href="/groups/{{ $group->slug }}/email-campaigns/create" class="btn btn-sm btn-secondary">@lang('campaigns.New campaign')</a>
    </div>

    <div class="card">
        <table class="table mb-0">
        <tr>
            <td><b>@lang('campaigns.Email Subject')</b></td>
            <td><b>@lang('campaigns.Created At')</b></td>
            <td><b>@lang('campaigns.Status')</b></td>
            <td><b>@lang('campaigns.Total Sent')</b></td>
            <td></td>
        </tr>
        @foreach($campaigns as $campaign)
        <tr>
            <td>{{ $campaign->email_subject }}</td>
            <td>{{ $campaign->created_at->tz(request()->user()->timezone)->format('F j, Y - g:ia') }}</td>
            <td>
                @if($campaign->status == 'sent')
                    <span class="badge badge-primary">@lang('campaigns.sent')</span>
                @else
                    <span class="badge badge-secondary">{{ $campaign->status }}</span>
                @endif
            </td>
            <td>{{ $campaign->total_sent }}</td>
            <td class="text-right">
                <a href="/groups/{{ $group->slug }}/email-campaigns/{{ $campaign->id }}">
                @if($campaign->status == 'sent')
                    @lang('campaigns.View')
                @else
                    @lang('campaigns.Edit/Send')
                @endif
                </a>
            </td>
        </tr>
        @endforeach
    </table>
    </div>

    <div class="d-flex justify-content-center">
        
    </div>
@endsection
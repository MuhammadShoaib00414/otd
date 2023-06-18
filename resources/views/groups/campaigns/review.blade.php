@extends('groups.layout')

@section('stylesheets')
    @parent
    
@endsection

@section('body-class', 'col-md-9')

@section('inner-content')
    <div class="d-flex mb-3mt-3">
        <h4 class="mb-0">@lang('campaigns.Review & Send'): {{ $campaign->email_subject }}</h4>
    </div>
    <div class="card" style="max-width: 700px;">
        <div class="card-body">
            <form method="post" action="/groups/{{ $group->slug }}/email-campaigns/{{ $campaign->id }}/send">
                @csrf
                @method('PUT')
                <div class="form-group">
                    @if($campaign->send_at)
                        <p class="mb-2"><b>@lang('campaigns.Scheduled to send at'):</b></p>
                        <p>{{ $campaign->send_at->tz(request()->user()->timezone)->format('m/d/y g:i a') }}</p>
                    @endif
                    <p class="mb-2"><b>@lang('campaigns.Send to groups')</b></p>
                    <ul>
                        @forelse($campaign->groups as $group)
                        <li>{{ $group->name }} ({{ $group->users()->count() }} @lang('campaigns.members'))</li>
                        @empty
                        <li>No groups selected</li>
                        @endforelse
                    </ul>
                    <p class="mb-2"><b>@lang('campaigns.Send to users')</b></p>
                    <ul>
                        @forelse($campaign->users as $user)
                        <li>{{ $user->name }}</li>
                        @empty
                        <li>@lang('campaigns.No users selected')</li>
                        @endforelse
                    </ul>
                    <p class="mb-2"><b>Total to send to</b></p>
                    <p>{{ $campaign->totalUsers }} @lang('campaigns.users')</p>
                </div>
                <hr>
                <div class="mt-3 text-right">
                    <button type="submit" class="btn btn-primary">@lang('campaigns.Send') <i class="fas fa-angle-right ml-1"></i></button>
                </div>
            </form>
        </div>
    </div>
@endsection
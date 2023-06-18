@extends('admin.emails.layout')

@section('head')
    <link rel="stylesheet" href="/revolvapp-1_0_7/css/revolvapp.min.css" />
@endsection

@section('inner-page-content')
<div>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mr-3 mb-0">{{ $campaign->email_subject }}</h5>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-6 mb-5">
            <form method="post" action="/admin/emails/campaigns/{{ $campaign->id }}/send">
                @csrf
                @method('post')
                <div class="form-group">
                    @if($campaign->send_at)
                        <p class="mb-2"><b>Scheduled to send at:</b></p>
                        <p>{{ $campaign->send_at->tz(request()->user()->timezone)->format('m/d/y g:i a') }}</p>
                    @endif
                    <p class="mb-2"><b>Send to groups</b></p>
                    <ul>
                        @forelse($campaign->groups as $group)
                        <li>{{ $group->name }} ({{ $group->users()->count() }} members)</li>
                        @empty
                        <li>No groups selected</li>
                        @endforelse
                    </ul>
                    <p class="mb-2"><b>Send to users</b></p>
                    <ul>
                        @forelse($campaign->users as $user)
                        <li>{{ $user->name }}</li>
                        @empty
                        <li>No users selected</li>
                        @endforelse
                    </ul>
                    <p class="mb-2"><b>Total to send to</b></p>
                    <p>{{ $campaign->totalUsers }} users</p>
                </div>
                <hr>
                <div class="mt-3 text-right">
                    <button type="submit" class="btn btn-primary">Send <i class="fas fa-angle-right ml-1"></i></button>
                </div>
            </form>
        </div>
        <div class="col-md-6 mb-5">
            <p class="mb-2"><b>Preview</b></p>
            <iframe src="/admin/emails/campaigns/{{ $campaign->id }}/html" style="width: 100%; height: 500px;"></iframe>
        </div>
    </div>
</div>
@endsection
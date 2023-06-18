@extends('admin.layout')

@section('head')
    <link rel="stylesheet" href="/revolvapp-1_0_7/css/revolvapp.min.css" />
@endsection

@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Email Campaigns' => '/admin/emails/campaigns',
        $campaign->email_subject => '/admin/emails/campaigns/' . $campaign->id,
    ]])
    @endcomponent

<div>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mr-3 mb-0">{{ $campaign->email_subject }}</h5>
            @if($campaign->status == 'sent')
                <span class="badge badge-primary">sent</span>
                <span>- {{ $campaign->total_sent }} emails sent at {{ $campaign->sent_at->tz(request()->user()->timezone)->format('F j, Y - g:ia') }}</span>
            @elseif($campaign->status == 'scheduled')
                <span class="badge badge-secondary">scheduled</span>
                <span>- scheduled to send at {{ $campaign->send_at->tz(request()->user()->timezone)->format('m/d/y g:i a') }}</span>
            @else
                <span class="badge badge-secondary">{{ $campaign->status }}</span>
            @endif
        </div>
        <div class="d-flex justify-content-end align-items-center">
            <form method="post" action="/admin/emails/campaigns/{{ $campaign->id }}">
                @csrf
                @method('delete')
                <button type="submit" class="btn btn-light mr-2" id="deleteCampaign">Delete</button>
            </form>
            <a href="/admin/emails/campaigns/{{ $campaign->id }}/duplicate" class="btn btn-light mr-2">Duplicate</a>
            @if($campaign->status == 'not sent')
            <a href="/admin/emails/campaigns/{{ $campaign->id }}/edit" class="btn btn-light mr-2">Edit</a>
            <div class="dropdown">
              <a href="/admin/emails/campaigns/{{ $campaign->id }}/send" class="btn btn-sm btn-primary dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Send
              </a>
              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="/admin/emails/campaigns/{{ $campaign->id }}/send">Send now</a>
                <a class="dropdown-item" href="/admin/emails/campaigns/{{ $campaign->id }}/schedule">Schedule</a>
              </div>
            </div>
            @endif
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-10 mb-5">
            <div>
                <p>
                    <b>Email Preview</b>
                </p>
                <iframe src="/admin/emails/campaigns/{{ $campaign->id }}/html" style="width: 100%; height: 500px;"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
  <script>
    $('#deleteCampaign').on('click', function(event) {
      event.preventDefault();
      if (confirm('Delete this campaign?'))
        $('#deleteCampaign').parent().submit();
    });
  </script>
@endsection
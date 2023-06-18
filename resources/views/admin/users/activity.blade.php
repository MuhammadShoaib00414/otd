@extends('admin.users.layout')

@section('inner-page-content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="m-0">Recent Activity</h5>
        <p class="m-0 font-italic text-muted">* Date and times are shown in users timezone</p>
    </div>

    <table class="table table-bordered" >
        <tr>
            <td><b>Datetime</b></td>
            <td><b>Action</b></td>
            <td><b>Target</b></td>
            <td><b>Description</b></td>
            <td><b>IP </b></td>
            <td><b>Location </b></td>
            <td><b>Browser </b></td>
            <td><b>Device </b></td>
            <td><b>Platform </b></td>

        </tr>
        @foreach($logs as $log)
      
        <tr>
            <td>{{ $log->timezone_adjusted_date->format('F j, Y - g:ia') }}</td>
            <td>{{ $log->action }}</td>
            <td>
                @if($log->relatedModel instanceOf App\User)
                    <a href="/admin/users/{{ $log->relatedModel->id }}">{{ $log->relatedModel->name }}</a>
                @elseif($log->relatedModelNoMatterWhat instanceOf App\Introduction)
                    @foreach($log->relatedModelNoMatterWhat->users as $introUser)
                        <a href="/admin/users/{{ $introUser->id }}">{{ $introUser->name }}</a>
                        @if (!$loop->last)
                         &
                        @endif
                    @endforeach
                @elseif($log->relatedModelNoMatterWhat instanceOf App\TextPost && $log->relatedModelNoMatterWhat->listing)
                    <a class="hoverable" tabindex="0" role="button" data-toggle="popover" data-trigger="focus" title="Groups: {{ $log->relatedModelNoMatterWhat->listing->groups->implode('name', ', ') }}" data-content="{{ $log->relatedModelNoMatterWhat->content }}" data-placement="top">{{ substr($log->relatedModelNoMatterWhat->content, 0, 40) }}...</a>
                @elseif($log->relatedModelNoMatterWhat instanceOf App\Shoutout && $log->relatedModelNoMatterWhat->listing)
                    <a class="hoverable" tabindex="0" role="button" data-toggle="popover" data-trigger="focus" title="Groups: {{ $log->relatedModelNoMatterWhat->listing->groups->implode('name', ', ') }}" data-content="{{ $log->relatedModelNoMatterWhat->body }}" data-placement="top">{{ $log->relatedModelNoMatterWhat->shouted->name }}</a>
                @elseif($log->relatedModelNoMatterWhat instanceOf App\Event) 
                    <a href="/admin/events/{{ $log->relatedModelNoMatterWhat->id }}">{{ $log->relatedModelNoMatterWhat->name }}</a>
                @elseif($log->relatedModelNoMatterWhat instanceOf App\ArticlePost) 
                    <a href="{{ $log->relatedModelNoMatterWhat->url }}">{{ $log->relatedModelNoMatterWhat->title }}</a>
                @elseif($log->relatedModelNoMatterWhat instanceOf App\Ideation)
                    @if($log->relatedModelNoMatterWhat->deleted_at != null)
                        {{ $log->relatedModelNoMatterWhat->name }} <i>(deleted)</i>
                    @else
                    <a href="/ideations/{{ $log->relatedModelNoMatterWhat->slug }}">{{ $log->relatedModelNoMatterWhat->name }}</a>
                    @endif
                @elseif($log->relatedModelNoMatterWhat instanceOf App\DiscussionThread)
                    @if($log->relatedModelNoMatterWhat->deleted_at != null)
                        {{ $log->relatedModelNoMatterWhat->name }} <i>(deleted)</i>
                    @elseif($log->relatedModelNoMatterWhat->group)
                    <a href="/groups/{{ $log->relatedModelNoMatterWhat->group->slug }}/discussions/{{ $log->relatedModelNoMatterWhat->slug }}">{{ $log->relatedModelNoMatterWhat->name }}</a>
                    @endif
                @elseif($log->relatedModelNoMatterWhat instanceOf App\Budget && $log->relatedModelNoMatterWhat->group)
                    @if($log->relatedModelNoMatterWhat->deleted_at != null)
                        {{ $log->relatedModelNoMatterWhat->name }} <i>(deleted)</i>
                    @else
                    <a href="/groups/{{ $log->relatedModelNoMatterWhat->group->slug }}/budgets/{{ $log->relatedModelNoMatterWhat->id }}">{{ $log->relatedModelNoMatterWhat->year }} Q{{ $log->relatedModelNoMatterWhat->quarter }}</a>
                    @endif
                @endif
            </td>
            <td>
            @if($log->action == 'Registeration')
                 {!! $log->message !!}
            @elseif($log->action == 'send invitation')
                 <a href="/invite/{!! $log->message !!}" target="_blank">Sent Invitation Link</a>
            @else
              {!! $log->message !!}
            @endif
           </td>
     
            <td> {{  $log->track_info ? $log->track_info['ip'] : '' }}</td>
            <td> {{  $log->track_info ? $log->track_info['location'] : '' }}</td>
            <td> {{  $log->track_info ? $log->track_info['browser'] : '' }}</td>
            <td> {{  $log->track_info ? $log->track_info['device'] : '' }}</td>
            <td> {{  $log->track_info ? $log->track_info['platform'] : '' }}</td>      
        </tr>
        @endforeach
    </table>

    <div class="d-flex justify-content-center">
        {{ $logs->links() }}
    </div>
@endsection
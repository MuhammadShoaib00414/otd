@extends('groups.layout')

@push('stylestack')
<style>
    figure img {
        max-width: 100%;
    }
</style>
@endpush


@section('inner-content')
    <div class="mb-2" style="font-size: 1.3em;">
        @lang('groups.Users that') <b>{{ $action }}</b>
    </div>
    <div class="card">
        <table class="table">
            <tr class="card-header">
                <td><b>@if($action == 'viewed event') @lang('general.event') @elseif($action == 'clicked content') @lang('general.content') @elseif($action == 'clicked subgroup') @lang('groups.subgroup') @elseif($action == 'clicked post link') @lang('general.post') @elseif($action == 'downloaded file') @lang('files.file') @else @lang('groups.users that') {{ $action }} @endif</b></td>
                <td class="text-right"><b>{{ $action == 'viewed event' || $action == 'clicked content' || $action == 'clicked subgroup' || $action == 'downloaded file' ? __('general.users') : __('groups.Views') }}</b></td>
                @if($action == 'viewed event')
                    <td></td>
                @endif
            </tr>
            <tbody>
                @if($action == 'viewed event')
                    @foreach($groupedClicks as $event)
                        <tr>
                            <td><a href="/groups/{{ $group->slug }}/events/{{ $event->id }}">{{ $event->name }}</a></td>
                            <td class="text-right">{{ $event->count }}</td>
                            <td class="text-right">@include('groups.activity.rsvpModal', ['users' => $event->users_clicked, 'name' => $event->name, 'id' => $event->id])</td>
                        </tr>
                    @endforeach
                @elseif($action == 'clicked content')
                    @foreach($groupedClicks as $article)
                        @if($article)
                            <tr>
                                <td><a href="{{ $article->url }}">{{ $article->title }}</a></td>
                                <td class="text-right">{{ $article->count }}</td>
                                <td class="text-right">@include('groups.activity.contentModal', ['article' => $article, 'users' => $article->users_clicked, 'name' => $article->title, 'id' => $article->id])</td>
                            </tr>
                        @endif
                    @endforeach
                @elseif($action == 'clicked subgroup')
                    @foreach($groupedClicks as $subgroup)
                        @if($subgroup)
                            <tr>
                                <td><a href="{{ $subgroup->url }}">{{ $subgroup->name }}</a></td>
                                <td class="text-right">{{ $subgroup->count }}</td>
                                <td class="text-right">@include('groups.activity.contentModal', ['subgroup' => $subgroup, 'users' => $subgroup->users_clicked, 'name' => $subgroup->title, 'id' => $subgroup->id])</td>
                            </tr>
                        @endif
                    @endforeach
                @elseif($action == 'clicked post link')
                    @foreach($groupedClicks as $post)
                        @if($post)
                            <tr>
                                <td><a style="display: block; max-width: 150px; overflow: hidden; white-space: nowrap;text-overflow: ellipsis;" href="/groups/{{ $group->slug }}/posts/{{ $post->id }}">{!! $post->post->content !!}</a></td>
                                <td class="text-right">{{ $post->count }}</td>
                                <td class="text-right">@include('groups.activity.textpostModal', ['post' => $post, 'users' => $post->users_clicked, 'name' => 'this post link', 'id' => $post->id])</td>
                            </tr>
                        @endif
                    @endforeach
                @elseif($action == 'downloaded file')
                    @foreach($groupedClicks as $file)
                        @if($file)
                            <tr>
                                <td><a style="display: block; max-width: 150px; overflow: hidden; white-space: nowrap;text-overflow: ellipsis;" href="/groups/{{ $group->slug }}/files">{{ $file->name }}</a></td>
                                <td class="text-right">{{ count($file->users_clicked) }}</td>
                                <td class="text-right">@include('groups.activity.fileDownloadModal', ['file' => $file, 'users' => $file->users_clicked, 'name' => $file->name, 'id' => $file->id])</td>
                            </tr>
                        @endif
                    @endforeach
                @else
                    @foreach($groupedClicks as $userId => $clicks)
                        <tr>
			    @if($clicks->first()->user)
			    <td><a href="/users/{{ $userId }}">{{ $clicks->first()->user->name }}</a></td>
			    @else
			    <td><i>User deleted</i></td>
			    @endif
                            <td class="text-right">{{ $clicks->count() }}</td>
			    @if($clicks->first()->user)
                            <td class="text-right"><a href="/users/{{ $userId }}">@lang('groups.view')</a></td>
			    @else
		 	    <td></td>
			    @endif
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
@endsection

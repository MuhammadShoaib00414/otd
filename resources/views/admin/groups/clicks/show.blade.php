@extends('admin.groups.layout')

@section('inner-page-content')
    <div class="mb-2" style="font-size: 1.3em;">
        Users that <b>{{ $action }}</b>
    </div>
    <div class="card">
        <table class="table">
            <tr class="card-header">
                <td><b>@if($action == 'viewed event') Event @elseif($action == 'clicked content') Content @else users that {{ $action }} @endif</b></td>
                <td class="text-right"><b>{{ $action == 'viewed event' || $action == 'clicked content' ? 'Users' : 'Views' }}</b></td>
                    <td></td>
            </tr>
            <tbody>
                @if($action == 'viewed event')
                    @foreach($groupedClicks as $event)
                        <tr>
                            <td><a href="/groups/{{ $group->slug }}/events/{{ $event->id }}">{{ $event->name }}</a></td>
                            <td class="text-right">{{ $event->count }}</td>
                            <td class="text-right">@include('groups.activity.rsvpModal', ['users' => $event->users_clicked, 'name' => $event->name,'id' => $event->id])</td>
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
                @else
                    @foreach($groupedClicks as $userId => $clicks)
                        <tr>
                            <td><a href="/admin/users/{{ $userId }}">{{ $clicks->first()->user->name }}</a></td>
                            <td class="text-right">{{ $clicks->count() }}</td>
                            <td class="text-right"><a href="/admin/users/{{ $userId }}">view</a></td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
@endsection
@extends('admin.reported.layout')

@section('inner-page-content')
<div class="col-md-6 mx-md-auto">
    <div class="card">
        <div class="card-body">
            <table class="w-100 table">
                <thead>
                    <tr>
                        <th>type</th>
                        <th>name</th>
                        <th class="text-right">count</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($postables as $postable)
                        <tr>
                            <td>
                                @if($postable instanceof App\Group)
                                    <i data-toggle="tooltip" data-placement="left" title="Group" class="fas fa-users fa-fw"></i>
                                @elseif($postable instanceof App\Ideation)
                                    <i data-toggle="tooltip" data-placement="left" title="Ideation" style="font-size: 1.2em;" class="fas fa-lightbulb ml-1 mr-2"></i>
                                @endif
                            </td>
                            <td>
                                @if($postable instanceof App\Group)
                                    <a href="/groups/{{ $postable->slug }}">{{ $postable->name }}</a>
                                @elseif($postable instanceof App\Ideation)
                                    @if(!$postable->reported_posts_count &&
                                        $postable->reported_articles_count)
                                        <a href="/ideations/{{ $postable->slug }}/articles">{{ $postable->name }}</a>
                                    @else
                                        <a href="/ideations/{{ $postable->slug }}">{{ $postable->name }}</a>
                                    @endif
                                @endif
                            </td>
                            <td class="text-right">{{ $postable->reported_count }}</td>
                            <td class="text-right">
                                @if($postable instanceof App\Group)
                                    <a href="/groups/{{ $postable->slug }}">view</a>
                                @elseif($postable instanceof App\Ideation)
                                    @if(!$postable->reported_posts_count &&
                                        $postable->reported_articles_count)
                                        <a href="/ideations/{{ $postable->slug }}/articles">View</a>
                                    @else
                                        <a href="/ideations/{{ $postable->slug }}">View</a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
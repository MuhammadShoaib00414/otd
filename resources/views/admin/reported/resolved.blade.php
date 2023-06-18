@extends('admin.reported.layout')

@section('inner-page-content')
<div class="col-md-6 mx-md-auto">
    <div class="card">
        <div class="card-body">
            <table class="w-100 table">
                <thead>
                    <tr>
                        <th>group</th>
                        <th class="text-right"># resolved</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groups as $group)
                        <tr>
                            <td><a href="/groups/{{ $group->slug }}">{{ $group->name }}</a></td>
                            <td class="text-right">{{ $group->resolved_posts->count() }}</td>
                            <td class="text-right"><a target="_blank" href="/groups/{{ $group->slug }}/resolved">view</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
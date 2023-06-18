@extends('admin.layout')

@section('head')
  <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
@endsection

@section('page-content')
<a href="/admin/content" class="d-inline-block mb-3"><i class="fas fa-angle-left"></i> All Content</a>

    <div class="d-flex align-items-start">
        <div class="card mb-4" style="max-width: 650px;">
            <div class="card-body">
                <div class="d-flex">
                    <a href="{{ $article->url }}">
                        <img src="{{ $article->image_url }}" id="image" class="mr-3" style="height: 100px; max-width: 100%;">
                    </a>
                    <div>
                        <p class="mb-0" style="font-size: 1.3em; font-weight: bold;">{{ $article->title }}</p>
                        <a href="{{ $article->url }}" class="text-muted">{{ $article->getRawOriginal('url') }}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="ml-3 w-50">
            <div>
                <a href="/admin/content/articles/{{ $article->id }}/edit" class="btn btn-primary">Edit</a>
                  <form action="/admin/content/articles/{{ $article->id }}" method="post" class="d-inline-block">
                      @method('delete')
                      @csrf
                      <button type="submit" class="btn btn-light ml-3" id="deleteEvent">Delete</button>
                  </form>
            </div>
            <hr>
            <div>
                <p><b>Groups</b></p>
                @foreach($article->listing->groups as $group)
                <a href="/admin/groups/{{ $group->id }}">{{ $group->name }}</a>@if(!$loop->last), @endif
                @endforeach
            </div>
        </div>
    </div>

        <p><b>Posted at:</b> {{ $article->listing->post_at->tz($authUser->timezone)->format('m/d/y - g:i a') }}</p>

        <hr>
        <p><b>Clicks</b> ({{ $article->clicks }})</p>
        <table class="table">
            <tr>
                <td><b>name</b></td>
                <td><b>date</b></td>
            <tr>
            @foreach($logs as $log)
            <tr>
                <td><a href="/admin/users/{{ $log->user->id }}">{{ $log->user->name }}</a></td>
                <td>{{ $log->created_at->format('m/d/y - g:i a') }}</td>
            </tr>
            @endforeach
        </table>
    </div>
    
@endsection

@section('scripts')
  <script>
    $('#deleteEvent').on('click', function(event) {
      event.preventDefault();
      if (confirm('Delete this content?'))
        $('#deleteEvent').parent().submit();
    });
  </script>
@endsection

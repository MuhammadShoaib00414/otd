@extends('admin.posts.layout')

@section('inner-page-content')
<div class="col-lg-12 col-md-12 col-sm-12 mb-5">
	<div class="card">
	    <table class="table my-0">
	        <thead>
	            <tr>
	                <th scope="col">post</th>
	                <th scope="col">title</th>
	                <th scope="col">posted</th>
	                <th scope="col" class="text-right"># of users</th>
	                <th scope="col" class="text-right">link clicks</th>
	                <th></th>
	            </tr>
	        </thead>
	        @foreach($posts as $post)
		        @if($post->post)
			        <tr>
			        	<td style="width: 1px; white-space: nowrap">
			        		@if($post->post instanceof App\TextPost)
			        			<i class="fas fa-archive fa-fw mr-1" data-toggle="tooltip" data-placement="top" title="Text Post"></i>
			        		@elseif($post->post instanceof App\ArticlePost)
			        			<i class="far fa-newspaper fa-fw mr-1" data-toggle="tooltip" data-placement="top" title="Content"></i>
			        		@elseif($post->post instanceof App\Event)
			        			<i class="far fa-calendar fa-fw mr-1" data-toggle="tooltip" data-placement="top" title="Event"></i>
			        		@elseif($post->post instanceof App\Shoutout)
			        			<i class="far fa-comment fa-fw mr-1" data-toggle="tooltip" data-placement="top" title="Shoutout"></i>
			        		@elseif($post->post instanceof App\DiscussionThread)
			        			<i class="far fa-comments fa-fw mr-1" data-toggle="tooltip" data-placement="top" title="Discussion"></i>
			        		@endif
			        	</td>
		        		<td>
			        		@if($post->post instanceof App\TextPost)
			        			<span>{!! strlimit(strip_tags($post->post->content), 75) !!}</span>
			        		@elseif($post->post instanceof App\ArticlePost)
			        			<span>{{ $post->post->title }}</span>
			        		@elseif($post->post instanceof App\Event)
			        			<span>{{ $post->post->name }}</span>
			        		@elseif($post->post instanceof App\Shoutout && $post->post->shouting)
			        			<span>{{ $post->post->shouting->name }} shouting {{ $post->post->shouted->name }}</span>
			        		@elseif($post->post instanceof App\DiscussionThread)
			        			<span>{{ $post->post->name }}</span>
			        		@endif
			        	</td>
			        	<td>{{ $post->post_at->diffForHumans() }}</td>
			        	<td class="text-right">{{ $post->total_user_count }}</td>
			        	<td class="text-right">
			        		@if($post->post instanceof App\TextPost)
			        			{{ $post->link_click_count }}
			        		@elseif($post->post instanceof App\ArticlePost)
			        			{{ $post->post->clicks }}
			        		@endif
			        	</td>
			        	<td class="text-right">
			        			<a target="_blank" href="{{ $post->url }}">view</a>
			        	</td>
			        </tr>
		        @endif
	        @endforeach
	    </table>
	</div>
	<div class="d-flex justify-content-center my-3">
		{{ $posts->links() }}
	</div>
</div>
@endsection
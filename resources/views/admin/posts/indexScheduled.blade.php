@extends('admin.posts.layout')

@section('inner-page-content')
<div class="col-lg-8 col-md-10 col-sm-12">
	<div class="text-right mb-2">
		<a href="/admin/posts/create" class="btn btn-sm btn-primary">New Scheduled Post</a>
	</div>
	<div class="card">
	    <table class="table my-0">
	        <thead>
	            <tr>
	                <th scope="col">post</th>
	                <th scope="col" class="text-right"># of users</th>
	                <th colspan="2"></th>
	            </tr>
	        </thead>
	        @foreach($posts as $scheduledPost)
		        @if($scheduledPost->post)
			        <tr>
			        	<td>
			        		@if($scheduledPost->post->post instanceof App\TextPost)
			        			<i class="fas fa-archive fa-fw mr-1" data-toggle="tooltip" data-placement="top" title="Text Post"></i>
			        		@else
			        			<i class="fas fa-newspaper fa-fw mr-1" data-toggle="tooltip" data-placement="top" title="Content"></i>
			        		@endif
			        		@if($scheduledPost->post->post instanceof App\TextPost)
			        			<span>{!! strlimit(strip_tags($scheduledPost->post->post->content), 75) !!}</span>
			        		@else
			        			<span>{{ $scheduledPost->post->post->title }}</span>
			        		@endif
			        	</td>
			        	<td class="text-right">{{ $scheduledPost->post->total_user_count }}</td>
			        	<td class="text-right">
			        		<a href="/admin/posts/{{ $scheduledPost->id }}/edit">Edit</a>
			        	</td>
			        </tr>
		        @endif
	        @endforeach
	    </table>
	</div>
</div>
@endsection
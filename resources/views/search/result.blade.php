@if($result->searchable instanceof App\User)
<tr class="py-2">
	<td style="width: 80px;"><a href="{{ $result->url }}"><div style="max-width: 3em; min-width: 3em; height: 3em; box-sizing: border-box; border-radius: 50%; background-image: url('{{ $result->searchable->photo_path }}'); background-size: cover; background-position: center; overflow: hidden;"></div></a>
	</td>
	<td>
		<a href="{{ $result->url }}">
			<b>{{ $result->title }}</b>
			@if($result->searchable->job_title && $result->searchable->company)
			<p><i>{{ $result->searchable->job_title }} at {{ $result->searchable->company }}</i></p>
			@endif
		</a>
	</td>
	<td style="vertical-align: middle" class="text-right"><a href="{{ $result->url }}">@lang('messages.view-user')</a></td>
</tr>
@elseif($result->searchable instanceof App\Ideation)
<tr class="py-2">
	<td style="vertical-align: middle; width: 80px; white-space: nowrap;"><a href="{{ $result->url }}" style="text-decoration: none"><i style="font-size: 1.9em;" class="fas fa-lightbulb fa-lg pl-2"></i></a>
	</td>
	<td>
		<a href="{{ $result->url }}">
			<b>{{ $result->title }}</b>
			<p class="mb-0">
				<i>{{ $result->searchable->participants()->count() . ' ' . str_plural('participant', $result->searchable->participants()->count()) }}</i>
			</p>
		</a>
	</td>
	<td style="vertical-align: middle" class="text-right"><a href="{{ $result->url }}">@lang('messages.view-ideation')</a></td>
</tr>
@elseif($result->searchable instanceof App\Event)
<tr class="py-2">
	<td style="vertical-align: middle; width: 80px; white-space: nowrap;"><a href="{{ $result->url }}" style="text-decoration: none"><i style="font-size: 1.9em;" class="fas fa-calendar fa-lg pl-2"></i></a>
	</td>
	<td>
		<a href="{{ $result->url }}">
			<b>{{ $result->title }}</b>
			@if($result->searchable->description)
				<p class="mb-0"><i>{{ $result->searchable->description }}</p>
			@endif
		</a>
	</td>
	<td style="vertical-align: middle" class="text-right"><a href="{{ $result->url }}">@lang('messages.view-event')</a></td>
</tr>
@elseif($result->searchable instanceof App\DiscussionThread)
<tr class="py-2">
	<td style="vertical-align: middle; width: 80px; white-space: nowrap;"><a href="{{ $result->url }}" style="text-decoration: none"><i style="font-size: 1.9em;" class="fas fa-comments fa-lg pl-2"></i></a>
	</td>
	<td>
		<a href="{{ $result->url }}">
			<b>{{ $result->title }}</b>
		</a>
	</td>
	<td style="vertical-align: middle" class="text-right"><a href="{{ $result->url }}" style="text-decoration: none">@lang('messages.view-discussion')</a></td>
</tr>
@elseif($result->searchable instanceof App\Group)
<tr class="py-2">
	<td style="vertical-align: middle; width: 60px; white-space: nowrap;"><a href="{{ $result->url }}" style="text-decoration: none"><i style="font-size: 1.9em;" class="fas fa-users fa-lg pl-2"></i></a>
	</td>
	<td>
		<a href="{{ $result->url }}">
			<b>{{ $result->title }}</b>
			<p class="mb-0"><i>{{ $result->searchable->users()->count() }} members</p>
		</a>
	</td>
	<td style="vertical-align: middle" class="text-right"><a href="{{ $result->url }}" style="text-decoration: none">@lang('messages.view-group')</a></td>
</tr>
@elseif($result->searchable instanceof App\ArticlePost)
<tr class="py-2">
	<td class="pl-0" style="width: 80px;"><a href="{{ $result->url }}"><img src="{{ $result->searchable->image_url }}" style="max-width: 75px; border-radius: 5px;"></a>
	</td>
	<td>
		<a href="{{ $result->url }}">
			<b>{{ str_limit($result->title, 75) }}</b>
		</a>
	</td>
	<td style="vertical-align: middle" class="text-right"><a href="{{ $result->url }}">@lang('general.view')</a></td>
</tr>
@endif
<div id="{{ $question->id }}" class="content my-2" data-type="{{ $question->parent_question_id ? $question->parent_question_id : '0' }}" style="background-color: hsl(204 12% 96% / 0.35); padding: 0.5em; border: {{ $count > 1 ? '1px solid #ccd4d4' : '' }}">
	<span>{{ $question->prompt }}</span>
	@if($question->children()->count())
		<div class="container-item mt-2">
			<div data-accepts="{{ $question->id }}" class="contains" style=" border: 1px solid #fff;">
				@foreach($question->children()->orderBy('order_key')->get() as $child)
					@include('admin.questions.partials.sort', ['question' => $child, 'count' => $count + 1, 'last' => $loop->last])
				@endforeach
			</div>
		</div>
	@endif
</div>
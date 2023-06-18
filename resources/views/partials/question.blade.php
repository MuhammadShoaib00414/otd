<div class="question {{ ($question->visible_when_parent_is && $question->visible_when_parent_is != optional(optional($authUser->questions->find($question->parent->id))->pivot)->answer) ? 'd-none' : 'd-block' }} {{ $question->visible_when_parent_is ? 'react-to-parent ml-2' : '' }}"{!! ($question->visible_when_parent_is) ? ' data-show="'.strtolower($question->visible_when_parent_is).'"' : '' !!}>
    @if($question->type == 'Text')
    <div class="form-group">
        <label>{{ $question->prompt }}</label>
        <input type="text" class="form-control" name="answers[{{ $question->id }}]" value="{{ optional(optional($authUser->questions->find($question->id))->pivot)->answer }}">
    </div>
    @elseif($question->type == 'Yes/No')
    <div class="form-group">
        <label class="d-block">{{ $question->prompt }}</label>
        <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="questionYes{{ $question->id }}" name="answers[{{ $question->id }}]" value="Yes" class="custom-control-inputss"{{ (optional(optional($authUser->questions->find($question->id))->pivot)->answer == 'Yes') ? ' checked' : '' }}>
          <label class="custom-control-label" for="questionYes{{ $question->id }}">@lang('general.yes')</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="questionNo{{ $question->id }}" name="answers[{{ $question->id }}]" value="No" class="custom-control-inputss"{{ (optional(optional($authUser->questions->find($question->id))->pivot)->answer == 'No') ? ' checked' : '' }}>
          <label class="custom-control-label" for="questionNo{{ $question->id }}">@lang('general.no')</label>
        </div>
    </div>
    @elseif($question->type == 'Dropdown menu' && collect($question->options)->whereNotNull()->count())
    <div class="form-group">
        <label>{{ $question->prompt }}</label>
        <select name="answers[{{ $question->id }}]" class="custom-select">
            <option {{ (optional(optional($authUser->questions->find($question->id))->pivot)->answer) ? '' : 'selected' }} disabled>@lang('general.select-one')</option>
            @foreach($question->options as $option)
                <option{{ (optional(optional($authUser->questions->find($question->id))->pivot)->answer == $option) ? ' selected' : '' }}>{{ $option }}</option>
            @endforeach
        </select>
    </div>
    @elseif($question->type == 'Date')
    <div class="form-group">
        <label>{{ $question->prompt }}</label>
        <input type="date" class="form-control" name="answers[{{ $question->id }}]" placeholder="mm/dd/yy" value="{{ optional(optional($authUser->questions->find($question->id))->pivot)->answer }}">
    </div>
    @elseif($question->type == 'Multi-select' && collect($question->options)->whereNotNull()->count())
    <div class="form-group">
        <label>{{ $question->prompt }}</label>
        @foreach($question->options as $option)
            <div class="form-check">
                <input type="checkbox" class="form-check-input" value="{{ $option }}" name="answers[{{ $question->id }}][]" id="option{{ $question->id }}{{ $loop->index }}"{{ (str_contains(optional(optional($authUser->questions->find($question->id))->pivot)->answer ?: '', $option)) ? ' checked' : '' }}>
                <label class="form-check-label" for="option{{ $question->id }}{{ $loop->index }}">{{ $option }}</label>
            </div>
        @endforeach
    </div>
    @endif
    <div class="children-questions">
        @each('partials.question', $question->children()->where('is_enabled', 1)->orderBy('order_key')->get(), 'question')
    </div>
</div>
@extends('admin.layout')

@section('page-content')

    @component('admin.partials.breadcrumbs', ['links' => [
        'Profile Questions' => '/admin/questions',
        $question->prompt => '/admin/questions/'.$question->id,
        'Edit' => '',
    ]])
    @endcomponent

    <div class="d-flex justify-content-between align-items-center">
        <h5>Edit Custom Question</h5>
        <form action="/admin/questions/{{ $question->id }}" method="post">
            @csrf
            @method('delete')
            <button type="submit" class="btn btn-sm btn-outline-danger" id="deleteButton">Delete</button>
        </form>
    </div>
    <div class="card">
        <div class="card-body" id="app">
            <form action="/admin/questions/{{ $question->id }}" method="post">
                @csrf
                @method('put')
                @if($question->parent)
                    <div class="form-group">
                        <label>Parent Question</label>
                        <p>
                            <a href="/admin/questions/{{ $question->parent_question_id }}" target="_blank">{{ $question->parent->prompt }}</a>
                        </p>
                    </div>
                    <div class="form-group">
                        <label class="d-block">Question visiblity</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" name="visibility" type="radio" id="alwaysVisible" value="always" v-model="visibility">
                            <label class="form-check-label" for="alwaysVisible">Always</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" name="visibility" type="radio" id="visibleWhenParentIs" value="sometimes" v-model="visibility">
                            <label class="form-check-label" for="visibleWhenParentIs">When parent is specific answer</label>
                        </div>
                    </div>
                    <div class="form-group" v-if="visibility == 'sometimes'">
                        <label for="visible_when_parent_is">Show when parent answer is:</label>
                        <input type="text" class="form-control" name="visible_when_parent_is" id="visible_when_parent_is" value="{{ $question->visible_when_parent_is }}">
                    </div>
                    <hr>
                @endif
                <div class="form-group w-50">
                    <label for="prompt">Prompt</label>
                    <input type="text" class="form-control" name="prompt" id="prompt" value="{{ $question->prompt }}" maxlength="600">
                </div>
                @if(getsetting('is_localization_enabled'))
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="locale" id="enlocale" value="en" {{ $question->locale == 'en' ? 'checked' : '' }}>
                      <label class="form-check-label" for="enlocale">
                        English
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="locale" value="es" id="eslocale" {{ $question->locale == 'es' ? 'checked' : '' }}>
                      <label class="form-check-label" for="eslocale">
                        Spanish
                      </label>
                    </div>
                @endif
                <div class="form-group w-50">
                    <label for="type">Answer Type</label>
                    <select name="type" id="type" class="custom-select" v-model="type">
                        <option>Text</option>
                        <option>Yes/No</option>
                        <option>Dropdown menu</option>
                        <option>Multi-select</option>
                        <option>Date</option>
                    </select>
                </div>
                <div v-if="type == 'Dropdown menu' || type == 'Multi-select'" class="mb-3">
                    <p><b>Answer Options</b></p>
                    <table>
                        <tr v-for="(answer, index) in answerOptions">
                            <td><input type="text" name="options[]" class="form-control form-control-sm" v-model="answer.value" :required="type == 'Dropdown menu' || type == 'Multi-select' ? true : false"></td>
                            <td><a href="#" class="btn btn-sm btn-outline-primary" @click.prevent="answerOptions.splice(index,1)">&times;</a></td>
                        </tr>
                    </table>
                    <a href="#" @click.prevent="answerOptions.push({value: ''})" class="btn btn-sm btn-outline-primary">Add</a>
                </div>
                <div class="custom-control custom-checkbox mb-2">
                    <input type="checkbox" class="custom-control-input" id="is_enabled" name="is_enabled" {{ $question->is_enabled ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_enabled">Enabled</label>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">@lang('general.save')</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    @if(config('app.env') == 'development')
        <script src="https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.js"></script>
    @else
        <script src="https://cdn.jsdelivr.net/npm/vue@2.6.12"></script>
    @endif
    <script>
        var vm = new Vue({
            el: '#app',
            data: {
                type: '{{ $question->type }}',
                visibility: "{{ $question->visible_when_parent_is ? 'sometimes' : 'always' }}",
                answerOptions: [],
            },
            created: function () {
                var options = JSON.parse(`{!! collect($question->options)->toJson() !!}`);
                for (i = 0; i < options.length; i++) {
                    this.answerOptions.push({value: options[i]});
                }
            }
        })
        $('#deleteButton').on('click', function(event) {
          event.preventDefault();
          if (confirm('Delete this question?'))
            $('#deleteButton').parent().submit();
        });
  </script>
@endsection
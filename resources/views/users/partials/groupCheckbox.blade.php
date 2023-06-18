<div>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" value="{{ $group->id }}" name="groups[]" id="group{{ $group->id }}"{{ $checked ? ' checked' : '' }}>
        <label class="form-check-label" for="group{{ $group->id }}" style="font-size: 16px;">
          {{ $group->name }}
        </label>
    </div>
    <div class="ml-2">
        @foreach($group->subgroups as $subgroup)
            @if($subgroup->is_joinable && !$subgroup->is_private || $subgroup->users()->where('user_id', request()->user()->id)->exists())
            @include('users.partials.groupCheckbox', [
                'group' => $subgroup,
                'checked' => $subgroup->users()->where('user_id', request()->user()->id)->exists(),
            ])
            @endif
        @endforeach
    </div>
</div>
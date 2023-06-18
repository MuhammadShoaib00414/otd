<div>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" value="{{ $group->id }}" name="groups[]" id="group{{ $group->id }}"{{ $checked ? ' checked' : '' }}>
        <label class="form-check-label" for="group{{ $group->id }}" style="font-size: 16px;">
          {{ $group->name }}
        </label>
    </div>
    <div class="ml-2">
        @foreach($group->subgroups as $subgroup)
            @include('admin.ideations.partials.groupcheckbox', [
                'group' => $subgroup,
                'ideation' => $ideation,
                'checked' => $ideation ? $ideation->groups->contains($subgroup->id) : false
            ])
        @endforeach
    </div>
</div>
<div>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" value="{{ $group->id }}" name="groups[]" id="group{{ $group->id }}"{{ $checked ? ' checked' : '' }}>
        <label class="form-check-label" for="group{{ $group->id }}" style="font-size: 16px;">
          {{ $group->name }}
        </label>
    </div>
    <div class="ml-2">
        @foreach($group->subgroups as $subgroup)
            @include('partials.groupcheckbox', [
                'group' => $subgroup,
                'checked' => (isset($segment) && isset($segment->filters) && isset($segment->filters->groups) && in_array($subgroup->id, $segment->filters->groups)) || (isset($checkedGroups) && $checkedGroups->contains($subgroup->id)),
                'segment' => isset($segment) ? $segment : null,
            ])
        @endforeach
    </div>
</div>
<div>
    <div class="form-check">
      <input id="group{{ $otherGroup->id }}" type="checkbox" name="groups[]" value="{{ $otherGroup->id }}">
      <label class="form-check-label" for="group{{ $otherGroup->id }}">
        {{ $otherGroup->name }}
      </label>
    </div>
    <div class="ml-2">
        @each('admin.groups.partials.groupcheckbox', $otherGroup->subGroups, 'otherGroup')
    </div>
</div>
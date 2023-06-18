<div>
    <div class="form-check mb-2">
      <input class="form-check-input" name="groups[]" id="eventgroup{{ $group->id }}" type="radio" value="{{ $group->id }}">
      <label class="form-check-label" for="eventgroup{{ $group->id }}">
        {{ $group->name }}
      </label>
    </div>
    <div class="ml-2">
        @each('admin.users.invites.partials.groupradiobutton', $group->subgroups, 'group')
    </div>
</div>
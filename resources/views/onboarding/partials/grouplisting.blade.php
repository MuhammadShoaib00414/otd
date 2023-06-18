<div>
    <div class="form-check">
        <input class="form-check-input groupInput" data-parent="{{ $group->parent_group_id }}" type="checkbox" value="{{ $group->id }}" name="groups[]" id="group{{ $group->id }}" {{ (!$authUser->groups->contains($group->id)) ? '' : 'checked' }}>
        <label class="form-check-label" for="group{{ $group->id }}" style="font-size: 16px;">
          {{ $group->name }}
        </label>
    </div>
    <div class="ml-2">
        @each('onboarding.partials.grouplisting', $group->joinable_subgroups, 'group')
    </div>
</div>
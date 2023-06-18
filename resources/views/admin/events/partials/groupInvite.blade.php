@foreach($count == 0 ? $allGroups->whereNull('parent_group_id') : $groups as $groupToInvite)
    <div class="form-check pl-0 mb-1">
      <input style="margin-left: {{ $count * 10 }}px;" type="checkbox" class="custom-checkbox" name="groups[]" value="{{ $groupToInvite->id }}" {{ (isset($event) && $event->isGroupInvited($groupToInvite->id)) ? 'checked' : '' }} {{ isset($disabledGroupId) && $disabledGroupId == $groupToInvite->id ? 'disabled checked' : '' }}>
      <label class="form-check-label" for="groups[]">{{ $groupToInvite->name }}</label>
    </div>
  @include('admin.events.partials.groupInvite', ['groups' => $allGroups->where('parent_group_id', $groupToInvite->id), 'count' => $count + 1, 'allGroups' => $allGroups])
@endforeach
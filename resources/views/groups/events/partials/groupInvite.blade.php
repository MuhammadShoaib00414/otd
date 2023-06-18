@foreach($count == 0 ? $allGroups->whereNull('parent_group_id')->where('is_events_enabled',1) : $groups as $groupToInvite)
  @if($group->id != $groupToInvite->id)
    <div class="form-check pl-0 mb-1">
      <input style="margin-left: {{ $count * 5 }}px;" type="checkbox" class="custom-checkbox" name="groups[]" value="{{ $groupToInvite->id }}">
      <label class="form-check-label" for="groups[]">{{ $groupToInvite->name }}</label>
    </div>
  @else
    <div class="form-check pl-0 mb-1">
      <input style="margin-left: {{ $count * 10 }}px;" disabled type="checkbox" class="custom-checkbox" name="groups[]" value="{{ $groupToInvite->id }}" checked>
      <label class="form-check-label" for="groups[]">{{ $groupToInvite->name }}</label>
    </div>
  @endif
  @include('groups.events.partials.groupInvite', ['groups' => $allGroups->where('parent_group_id', $groupToInvite->id)->where('is_events_enabled',1), 'group' => $group, 'count' => $count + 1, 'allGroups' => $allGroups])
@endforeach
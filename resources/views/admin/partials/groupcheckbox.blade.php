<div class="form-check mb-2" style="margin-left: {{ $count * 8 }}px;">
  <input type="checkbox" name="groups[]" value="{{ $group->id }}" id="group{{ $group->id }}" {{ isset($checked) && $checked ? 'checked' : '' }}>
  <label class="form-check-label" for="group{{ $group->id }}">
    {{ $group->name }}
  </label>
</div>
@foreach($group->subgroups as $subgroup)
    @include('admin.partials.groupCheckbox', ['group' => $subgroup, 'count' => $count + 1, 'checked' => getsetting('or_event_only_groups') && collect(json_decode(getsetting('or_event_only_groups')))->contains($subgroup->id)])
@endforeach
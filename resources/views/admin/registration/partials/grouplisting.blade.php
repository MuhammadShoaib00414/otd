<tr>
	<td style="padding-left: {{ 8*$count }}px;">
		<label class="form-check-label" for="group{{ $group->id }}">
    		{{ $group->name }}
  		</label>
	</td>
	<td class="text-center">
  		<input class="form-check-input group" name="groups[]" id="group{{ $group->id }}" type="checkbox" value="{{ $group->id }}" data-parent-group-id="{{ $group->parent_group_id }}" data-group-id="{{ $group->id }}" {{ isset($checkedGroups) && collect($checkedGroups)->contains($group->id) ? 'checked' : '' }}>
  	</td>
</tr>
@foreach($group->subgroups as $subgroup)
    @include('admin.registration.partials.grouplisting', ['group' => $subgroup, 'count' => $count + 1])
 @endforeach
<tr id="{{ $group->id }}" class="{{ ($count > 0 && $topParentGroupId) ? 'belongsTo'.$topParentGroupId : '' }} {{ $group->parent_group_id ? 'd-none' : '' }}">
    @if($group->parent_group_id == null && $group->subgroups()->count())
    <td onclick="toggleChildren('{{ $group->id }}')" style="{{ $group->subgroups()->count() ? 'cursor: pointer;' : '' }}">
    @else
    <td>
    @endif
        {{ str_repeat(' -', $count) }}
        @if($group->parent_group_id == null && $group->subgroups()->count())
            <i class="fas fa-angle-right"></i>
        @endif
        {{ $group->name }}
    </td>
    <td class="text-center">
        <input class="form-check-input ml-0 membership-checkbox" type="checkbox" value="{{ $group->id }}" data-immediate-parent="{{ $group->parent_group_id }}" data-group-id="{{ $group->id }}" name="groups[]" id="group{{ $group->id }}" {{ (!$user->groups->contains($group->id)) ?: 'checked=""' }}>
    </td>
    <td class="text-center">
        <input id="admin{{ $group->id }}" data-group-id="{{ $group->id }}" class="form-check-input ml-0 admin-checkbox" type="checkbox" value="{{ $group->id }}" name="groupsIsAdminOf[]" {{ ($user->groups->contains($group->id) && $user->groups()->find($group->id)->isUserAdmin($user->id, false)) ? 'checked=""' : '' }}>
    </td>
</tr>
@if($group->subgroups()->count())
    @foreach($group->subgroups as $subgroup)
        @include('admin.users.partials.groupsRecursive', ['group' => $subgroup, 'count' => $count + 1, 'topParentGroupId' => $topParentGroupId ?: $group->id])
    @endforeach
@endif
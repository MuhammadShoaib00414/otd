@foreach($group->subgroups as $subgroup)
    <tr class="{{ $topParentId }}children d-none">
        <td class="pl-3">{{ str_repeat(' -', $count) . ' ' . $subgroup->name }}
            @if($subgroup->deleted_at)
                <span class="badge badge-danger">deleted</span>
            @endif
        </td>
        <td>{{ $subgroup->users()->count() }}</td>
        <td class="text-right"><a href="/admin/groups/{{ $subgroup->id }}">View</a></td>
    </tr>
    @include('admin.groups.partials.indexRecursive', ['group' => $subgroup, 'count' => $count + 1, 'topParentId' => $topParentId])
@endforeach
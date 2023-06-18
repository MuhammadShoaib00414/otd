<div>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" value="{{ $group->id }}" name="groups[]" id="group{{ $group->id }}"{{ ((isset($post) && $post->groups()->where('id', $group->id)->count()) || (isset($checkedGroups) && $checkedGroups->contains($group->id))) ? ' checked' : '' }}>
        <label class="form-check-label" for="group{{ $group->id }}" style="font-size: 16px;">
          {{ $group->name }}
        </label>
    </div>
    <div class="ml-3">
        @foreach($group->subgroups->where('is_content_enabled',1) as $subgroup)
            @include('admin.posts.partials.getShareableGroups', [
                'group' => $subgroup,
                'post' => isset($post) ? $post : null,
                'checkedGroups' => isset($checkedGroups) ? $checkedGroups : null,
            ]) 
        @endforeach
    </div>
</div>
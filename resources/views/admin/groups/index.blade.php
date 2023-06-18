@extends('admin.layout')

@section('head')
@parent
<style>
    .rotated {
        -webkit-transform: rotate(90deg);
        -moz-transform: rotate(90deg);
        -ms-transform: rotate(90deg);
        -o-transform: rotate(90deg);
        transform: rotate(90deg); 
    }
</style>
@endsection

@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Groups' => '/admin/groups'
    ]])
    @endcomponent

    <div class="d-flex justify-content-between">
        <div class="d-flex align-items-center justify-content-start">
            <h5 class="mr-4 mb-0">Groups</h5>
        </div>
        <div class="text-right">
            <a class="btn btn-outline-primary btn-sm mr-2" href="/admin/groups/bulk-settings">Bulk Group Settings</a>
            <a class="btn btn-outline-primary btn-sm mr-2" href="/admin/groups/sort">Sort Groups</a>
            <a class="btn btn-primary btn-sm" href="/admin/groups/create">
              Add Group
            </a>
        </div>
    </div>
    
    <table class="table mt-2">
        <thead>
            <tr>
                <th scope="col"><b>Name</b></th>
                <th scope="col"><b>Members</b></th>
                <th></th>
            </tr>
        </thead>
        @foreach($groups->sortBy('name') as $group)
            <tr>
                <td class="top_level_group" data-id="{{ $group->id }}" style="{{ $group->subgroups()->count() ? 'cursor: pointer;': ''}}">
                    @if($group->subgroups()->count())
                        <i class="fas fa-angle-right" id="caret{{ $group->id }}"></i>
                    @endif
                    {{ $group->name }}
                    @if($group->deleted_at)
                        <span class="badge badge-danger">deleted</span>
                    @endif
                </td>
                <td class="top_level_group" data-id="{{ $group->id }}" style="{{ $group->subgroups()->count() ? 'cursor: pointer;': ''}}">{{ $group->users()->count() }}</td>
                <td class="text-right"><a href="/admin/groups/{{ $group->id }}">View</a></td>
            </tr>
            @include('admin.groups.partials.indexRecursive', ['group' => $group, 'count' => 1, 'topParentId' => $group->id])
        @endforeach
    </table>
    @if(request()->has('deleted'))
    <a href="/admin/groups" class="btn btn-sm btn-outline-primary">Show live groups</a>
    @else
    <a href="/admin/groups?deleted=show" class="btn btn-sm btn-outline-primary">Show deleted groups</a>
    @endif
    <!-- <a href="/admin/make-groups" class="btn btn-sm btn-outline-primary">Make groups script</a> -->
@endsection

@section('scripts')
<script>
    $('.top_level_group').click(function(e) {
        $('.'+$(this).data('id')+'children').toggleClass('d-none');
        $('#caret'+$(this).data('id')).toggleClass('rotated');
    });
</script>
@endsection
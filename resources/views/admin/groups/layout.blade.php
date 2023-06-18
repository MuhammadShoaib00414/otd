@extends('admin.layout')

@section('page-content')
@component('admin.partials.breadcrumbs', ['links' => [
        'Groups' => '/admin/groups',
        $group->name => '',
    ]])
    @endcomponent

<div style="background-color: #fafbfd; margin: -1em -1em 1em -1em; padding: 1em 0 0; border-left: 1px solid #e0e2e7;">
    <div class="d-flex justify-content-between align-items-center mb-3 px-3">
        <div class="d-flex justify-content-between align-items-center ml-2">
          <div>
            @if($group->parent)
            <p class="text-small text-muted mb-2">Parent: <a href="/admin/groups/{{ $group->parent->id }}">{{ $group->parent->name }}</a></p>
            @endif
            <h5>{{ $group->name }}</h5>
          </div>
        </div>
    </div>

    <ul class="nav nav-tabs mb-4 px-3">
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('admin/groups/' . $group->id)) ? ' active' : '' }}" href="/admin/groups/{{ $group->id }}">Overview</a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('*users*')) ? ' active' : '' }}" href="/admin/groups/{{ $group->id }}/users">Users</a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('*budgets*')) ? ' active' : '' }}" href="/admin/groups/{{ $group->id }}/budgets">Budgets</a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('*files*')) ? ' active' : '' }}" href="/admin/groups/{{ $group->id }}/files">Files</a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('*subgroups*')) ? ' active' : '' }}" href="/admin/groups/{{ $group->id }}/subgroups">Subgroups</a>
        </li>
        @if($group->is_virtual_room_enabled)
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('*virtual-room*')) ? ' active' : '' }}" href="/admin/groups/{{ $group->id }}/virtual-room">Interactive Header Image</a>
        </li>
        @endif
        @if($group->is_lounge_enabled)
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('*lounge*')) ? ' active' : '' }}" href="/admin/groups/{{ $group->id }}/lounge">Lounge</a>
        </li>
        @endif
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('*activity*')) ? ' active' : '' }}" href="/admin/groups/{{ $group->id }}/activity">Activity</a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('*settings*')) ? ' active' : '' }}" href="/admin/groups/{{ $group->id }}/settings">Settings</a>
        </li>
    </ul>
</div>

    @yield('inner-page-content')

@endsection

@section('scripts')
    <script>
    $(function () {
        $('[data-toggle="popover"]').popover({
            trigger: 'focus'
        });
    });
    </script>
@endsection
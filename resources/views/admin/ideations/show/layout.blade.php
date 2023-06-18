@extends('admin.layout')

@section('page-content')
<div style="background-color: #fafbfd; margin: -1em -1em 1em -1em; padding: 1em 0 0; border-left: 1px solid #e0e2e7;">
    <div class="d-flex justify-content-between align-items-center mb-3 px-3">
        <div class="d-flex align-items-center ml-2">
          <div>
            <h5>{{ $ideation->name }}</h5>
          </div>
        </div>
        @if($ideation->deleted_at)
            <form action="/admin/ideations/{{ $ideation->id }}/restore" method="post">
                @csrf
                @method('put')
                <button type="submit" class="btn btn-sm btn-outline-primary" onclick="return confirm('Are you sure you want to restore {{ $ideation->name }}?');">Restore</button>
            </form>
        @endif
    </div>

    <ul class="nav nav-tabs mb-4 px-3">
        <li class="nav-item">
            <a class="nav-link{{ (!Request::is('admin/ideations/*/*')) ? ' active' : '' }}" href="/admin/ideations/{{ $ideation->id }}">Overview</a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('*members*') && !Request::is('*invite*')) ? ' active' : '' }}" href="/admin/ideations/{{ $ideation->id }}/members">Members</a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('*invitations*') || Request::is('*invite*')) ? ' active' : '' }}" href="/admin/ideations/{{ $ideation->id }}/invitations">Invitations</a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('*files*')) ? ' active' : '' }}" href="/admin/ideations/{{ $ideation->id }}/files">Files</a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('*edit*')) ? ' active' : '' }}" href="/admin/ideations/{{ $ideation->id }}/edit">Settings</a>
        </li>
    </ul>
</div>

    @yield('inner-page-content')

@endsection
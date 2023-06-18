@extends('admin.layout')

@section('page-content')

    @component('admin.partials.breadcrumbs', ['links' => [
        'Posts' => '/admin/posts',
    ]])
    @endcomponent

<div style="background-color: #fafbfd; margin: -1em -1em 1em -1em; padding: 1em 0 0; border-left: 1px solid #e0e2e7;">
    <div class="d-flex justify-content-between align-items-center mb-3 px-3">
        <div class="d-flex justify-content-between align-items-center ml-2">
          <div>
            <h5>Posts</h5>
          </div>
        </div>
    </div>

    <ul class="nav nav-tabs mb-4 px-3">
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('admin/posts')) ? ' active' : '' }}" href="/admin/posts">Posts</a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('admin/posts/scheduled')) ? ' active' : '' }}" href="/admin/posts/scheduled">Scheduled</a>
        </li>
    </ul>
</div>

    @yield('inner-page-content')

@endsection
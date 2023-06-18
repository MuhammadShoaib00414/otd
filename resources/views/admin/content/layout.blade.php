@extends('admin.layout')

@section('page-content')
<div style="background-color: #fafbfd; margin: -1em -1em 1em -1em; padding: 1em 0 0; border-left: 1px solid #e0e2e7;">
    <div class="d-flex justify-content-between align-items-center mb-3 px-3">
        <div class="d-flex align-items-center ml-2">
          <div>
            <h5>Content</h5>
          </div>
        </div>
    </div>

    <ul class="nav nav-tabs mb-4 px-3">
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('admin/content')) ? ' active' : '' }}" href="/admin/content">Content</a>
        </li>
    </ul>
</div>

    @yield('inner-page-content')

@endsection

@section('scripts')

@endsection
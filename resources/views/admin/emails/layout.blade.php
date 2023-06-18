@extends('admin.layout')

@push('stylestack')
<style>
.rex-toolbar-container.rex-toolbar-sticky{
  z-index: 0 !important;
}
</style>
@endpush

@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Notifications' => '/admin/emails/notifications',
    ]])
    @endcomponent

<div style="background-color: #fafbfd; margin: -1em -1em 1em -1em; padding: 1em 0 0; border-left: 1px solid #e0e2e7;">
  <div class="ml-3 mb-3">
    <h5>Notifications</h5>
  </div>

    <ul class="nav nav-tabs mb-4 px-3">
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('*/emails/notifications*')) ? ' active' : '' }}" href="/admin/emails/notifications">Email Notifications</a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('*push*')) ? ' active' : '' }}" href="/admin/notifications/push">Push Notifications</a>
        </li>
        <!--
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('*lists*')) ? ' active' : '' }}" href="/admin/emails/lists">Lists</a>
        </li>
        -->
    </ul>
</div>

    @yield('inner-page-content')

@endsection
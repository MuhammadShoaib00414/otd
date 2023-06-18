@extends('admin.layout')

@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Profile Categories' => '/admin/categories',
    ]])
    @endcomponent

<div style="background-color: #fafbfd; margin: -1em -1em 1em -1em; padding: 1em 0 0; border-left: 1px solid #e0e2e7;">
  <div class="d-flex justify-content-between align-items-center mx-3 mb-3">
    <h5>Profile Categories</h5>
    <div>
        <a href="/admin/categories/sort" class="btn btn-sm btn-outline-primary">Sort</a>
        <a href="/admin/categories/create" class="btn btn-sm btn-outline-primary">New</a>
    </div>  
  </div>

    <ul class="nav nav-tabs mb-4 px-3">
        @foreach(App\Taxonomy::all() as $taxonomy)
            <li class="nav-item">
                <a class="nav-link{{ (Request::is('*categories/'.$taxonomy->id.'/*') || Request::is('*categories/'.$taxonomy->id)) ? ' active' : '' }}" href="/admin/categories/{{ $taxonomy->id }}">{{ $taxonomy->name }}</a>
            </li>
        @endforeach
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('*approval*')) ? ' active' : '' }}" href="/admin/categories/approval">Approval Queue</a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('*expense-categories*')) ? ' active' : '' }}" href="/admin/categories/expense-categories">Expenses</a>
        </li>
        @if(\App\Setting::where('name', 'is_departments_enabled')->first()->value)
            <li class="nav-item">
                <a class="nav-link{{ (Request::is('*departments*')) ? ' active' : '' }}" href="/admin/categories/departments">Departments</a>
            </li>
        @endif
        @if(\App\Setting::where('name', 'is_management_chain_enabled')->first()->value)
            <li class="nav-item">
                <a class="nav-link{{ (Request::is('*titles*')) ? ' active' : '' }}" href="/admin/categories/titles">Management Chain</a>
            </li>
        @endif
    </ul>
</div>

    @yield('inner-page-content')

@endsection
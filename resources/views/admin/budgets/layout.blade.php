@extends('admin.layout')

@section('page-content')

    @component('admin.partials.breadcrumbs', ['links' => [
        'Budgets' => '/admin/budgets',
    ]])
    @endcomponent

<div style="background-color: #fafbfd; margin: -1em -1em 1em -1em; padding: 1em 0 0; border-left: 1px solid #e0e2e7;">
    <div class="d-flex justify-content-between align-items-center mb-3 px-3">
        <div class="d-flex align-items-center ml-2">
          <div style="height: 5em; width: 5em; border-radius: 50%; background-image: url('{{ $user->photo_path }}'); background-size: cover; background-position: center;"></div>
          <div class="ml-3">
            <h5>Budgets</h5>
          </div>
        </div>
        <div>
            @if(Request::is('admin/budgets/' . $user->id))
                <a class="btn btn-primary btn-sm" href="/admin/budgets/create">Add Budget</a>
            @endif
        </div>
    </div>

    <ul class="nav nav-tabs mb-4 px-3">
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('admin/users/' . $user->id)) ? ' active' : '' }}" href="/admin/users/{{ $user->id }}">Details</a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('*groups*')) ? ' active' : '' }}" href="/admin/users/{{ $user->id }}/groups">Groups</a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('*activity*')) ? ' active' : '' }}" href="/admin/users/{{ $user->id }}/activity">Activity Log</a>
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